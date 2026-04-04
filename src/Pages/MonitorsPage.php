<?php

namespace Ziming\FilamentOhDear\Pages;

use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Throwable;

class MonitorsPage extends BaseOhDearPage implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $title = 'Monitors';

    protected static ?string $slug = 'oh-dear/monitors';

    protected string $view = 'filament-oh-dear::pages.monitors';

    public bool $requiresSetup = false;

    public ?string $loadError = null;

    protected static function getNavigationSortOffset(): int
    {
        return 1;
    }

    /**
     * @return string | array<string>
     */
    public static function getNavigationItemActiveRoutePattern(): string|array
    {
        return [
            static::getRouteName(),
            ViewMonitorPage::getRouteName(),
        ];
    }

    public function mount(): void
    {
        $this->mountInteractsWithTable();

        if (! $this->settings()->isConfigured()) {
            $this->requiresSetup = true;
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->defaultSort('display_name')
            ->paginationPageOptions([10, 25, 50, 'all'])
            ->records(fn (
                ?string $search,
                array $filters,
                ?string $sortColumn,
                ?string $sortDirection,
                int|string $page,
                int|string $recordsPerPage
            ): LengthAwarePaginator => $this->getTableData(
                $search,
                $filters,
                $sortColumn,
                $sortDirection,
                (int) $page,
                $recordsPerPage,
            ))
            ->columns([
                TextColumn::make('display_name')
                    ->label('Monitor')
                    ->description(fn (array $record): string => $record['url'])
                    ->wrap()
                    ->sortable(),
                TextColumn::make('type_label')
                    ->label('Type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('group_label')
                    ->label('Group')
                    ->toggleable(),
                TextColumn::make('tags_label')
                    ->label('Tags')
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('result_label')
                    ->label('Result')
                    ->badge()
                    ->color(fn (array $record): string => $record['result_color'])
                    ->sortable(),
                TextColumn::make('latest_run_display')
                    ->label('Latest run')
                    ->sortable(),
                TextColumn::make('uptime_summary')
                    ->label('Uptime')
                    ->wrap(),
                TextColumn::make('performance_summary')
                    ->label('Performance')
                    ->wrap(),
                TextColumn::make('certificate_summary')
                    ->label('Certificate')
                    ->wrap(),
                TextColumn::make('broken_links_summary')
                    ->label('Broken links')
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('result')
                    ->options([
                        'succeeded' => 'Healthy',
                        'warning' => 'Warning',
                        'failed' => 'Failed',
                        'errored-or-timed-out' => 'Timed out',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        'http' => 'HTTP',
                        'ping' => 'Ping',
                        'tcp' => 'TCP',
                    ]),
                SelectFilter::make('group')
                    ->options($this->groupFilterOptions()),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('View details')
                    ->color('gray')
                    ->url(fn (array $record): string => ViewMonitorPage::getUrl(['monitor' => $record['id']])),
            ])
            ->emptyStateHeading('No monitors matched the current filters.')
            ->emptyStateDescription('Try clearing the search or filters, or adjust the configured Oh Dear scope.');
    }

    /**
     * @return array<string, string>
     */
    protected function groupFilterOptions(): array
    {
        try {
            return collect($this->dataService()->getMonitorGroups($this->settings()))
                ->mapWithKeys(static fn (string $group): array => [$group => $group])
                ->all();
        } catch (Throwable) {
            return [];
        }
    }

    protected function getTableData(
        ?string $search,
        array $filters,
        ?string $sortColumn,
        ?string $sortDirection,
        int $page,
        int|string $recordsPerPage,
    ): LengthAwarePaginator {
        if ($this->requiresSetup) {
            return $this->emptyPaginator($page, $recordsPerPage);
        }

        try {
            $records = $this->tableRecords()
                ->when(
                    filled($search),
                    fn (Collection $records): Collection => $this->applySearch($records, (string) $search),
                )
                ->when(
                    filled($result = data_get($filters, 'result.value')),
                    fn (Collection $records): Collection => $records->filter(
                        fn (array $record): bool => $record['result'] === $result,
                    ),
                )
                ->when(
                    filled($type = data_get($filters, 'type.value')),
                    fn (Collection $records): Collection => $records->filter(
                        fn (array $record): bool => $record['type'] === $type,
                    ),
                )
                ->when(
                    filled($group = data_get($filters, 'group.value')),
                    fn (Collection $records): Collection => $records->filter(
                        fn (array $record): bool => $record['group_label'] === $group,
                    ),
                );

            if (filled($sortColumn)) {
                $records = $this->sortRecords($records, $sortColumn, $sortDirection);
            }

            return $this->paginate($records, $page, $recordsPerPage);
        } catch (Throwable $exception) {
            $this->loadError = $exception->getMessage();

            return $this->emptyPaginator($page, $recordsPerPage);
        }
    }

    #[Computed]
    public function tableRecords(): Collection
    {
        return $this->dataService()->getMonitorTableRows($this->settings());
    }

    protected function applySearch(Collection $records, string $search): Collection
    {
        $needle = Str::lower($search);

        return $records->filter(function (array $record) use ($needle): bool {
            return collect([
                $record['display_name'],
                $record['url'],
                $record['group_label'],
                $record['tags_label'],
            ])->contains(
                static fn (?string $value): bool => filled($value) && str_contains(Str::lower((string) $value), $needle),
            );
        });
    }

    protected function sortRecords(Collection $records, string $sortColumn, ?string $sortDirection): Collection
    {
        $direction = $sortDirection === 'desc';

        return $records->sortBy(
            fn (array $record): mixed => match ($sortColumn) {
                'display_name' => Str::lower($record['display_name']),
                'type_label' => Str::lower($record['type_label']),
                'group_label' => Str::lower($record['group_label']),
                'result_label' => Str::lower($record['result_label']),
                'latest_run_display' => $record['latest_run_at'] ?? '',
                default => $record[$sortColumn] ?? '',
            },
            SORT_NATURAL,
            $direction,
        );
    }

    protected function paginate(Collection $records, int $page, int|string $recordsPerPage): LengthAwarePaginator
    {
        $total = $records->count();
        $perPage = $recordsPerPage === 'all' ? max($total, 1) : (int) $recordsPerPage;

        return new LengthAwarePaginator(
            $records->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['pageName' => $this->getTablePaginationPageName()],
        );
    }

    protected function emptyPaginator(int $page, int|string $recordsPerPage): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            collect(),
            0,
            $recordsPerPage === 'all' ? 1 : max(1, (int) $recordsPerPage),
            $page,
            ['pageName' => $this->getTablePaginationPageName()],
        );
    }
}
