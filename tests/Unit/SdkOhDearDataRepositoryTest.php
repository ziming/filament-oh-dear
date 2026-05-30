<?php

use OhDear\PhpSdk\Exceptions\OhDearException;
use OhDear\PhpSdk\Requests\LighthouseReports\GetLatestLighthouseReportRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Ziming\FilamentOhDear\Repositories\SdkOhDearDataRepository;
use Ziming\FilamentOhDear\Support\OhDearDataNormalizer;
use Ziming\FilamentOhDear\Support\OhDearSettings;

beforeEach(function () {
    $this->repository = new SdkOhDearDataRepository(new OhDearDataNormalizer);
    $this->settings = new OhDearSettings(
        apiToken: 'token',
        teamId: 10,
        monitorIds: [1],
        cacheStore: 'array',
        cacheTtl: 300,
        navigationGroup: 'Oh Dear',
        navigationIcon: 'heroicon-o-signal',
        navigationSort: 50,
    );
});

afterEach(function () {
    MockClient::destroyGlobal();
});

it('returns null when no latest lighthouse report exists', function () {
    MockClient::global([
        GetLatestLighthouseReportRequest::class => MockResponse::make(['message' => 'Not found'], 404),
    ]);

    expect($this->repository->latestLighthouseReport($this->settings, 1))->toBeNull();
});

it('rethrows non-not-found latest lighthouse report failures', function () {
    MockClient::global([
        GetLatestLighthouseReportRequest::class => MockResponse::make(['message' => 'Server error'], 500),
    ]);

    $this->repository->latestLighthouseReport($this->settings, 1);
})->throws(OhDearException::class);
