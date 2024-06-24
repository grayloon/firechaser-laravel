<?php

uses()->group('controller', 'sync');

use Composer\InstalledVersions;
use GrayLoon\FireChaser\Http\Controllers\FireChaserSyncController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use function Pest\Laravel\{get};

$key = 'abcdefghijklmnopqrstuvwxyz1234567890';

beforeEach(function () use ($key) {
    Config::set('firechaser.api_site_key', $key);
    Config::set('app.env', 'production');
    Config::set('app.debug', false);
});

it('fails when not in "production"', function (string $environment) {
    Config::set('app.env', $environment);

    get(route('firechaser.sync'))
        ->assertStatus(500)
        ->assertContent('Application not in production or missing API Site Key.');
})->with(['local', 'testing']);

it('fails when no API Site Key is saved.', function () {
    Config::set('firechaser.api_site_key', null);

    get(route('firechaser.sync'))
        ->assertStatus(500)
        ->assertContent('Application not in production or missing API Site Key.');
});

it('fails when no API Site Key is given from request.', function () {
    get(route('firechaser.sync'))
        ->assertStatus(500)
        ->assertContent('Application not in production or missing API Site Key.');
});

it('fails when API Site Keys do not match.', function () {
    get(route('firechaser.sync') . '?api_site_key=1234567890')
        ->assertStatus(403)
        ->assertContent('Invalid API Site Key given.');
});

it('fails when no composer packages found', function () use ($key) {
    $mockedComposer = Mockery::mock(InstalledVersions::class);
    $mockedComposer->shouldReceive('getAllRawData')
        ->andReturn([]);

    $request = Request::create(
        uri: route('firechaser.sync') . '?api_site_key=' . $key,
    );

    $response = (new FireChaserSyncController(composer: $mockedComposer))->__invoke($request);

    expect($response->status())->toBe(500)
        ->and($response->content())->toBe('There was an error fetching composer packages.');
});

it('fails when no composer package versions found', function () use ($key) {
    $mockedComposer = Mockery::mock(InstalledVersions::class);
    $mockedComposer->shouldReceive('getAllRawData')
        ->andReturn([
            ['test' => []]
        ]);

    $request = Request::create(
        uri: route('firechaser.sync') . '?api_site_key=' . $key,
    );

    $response = (new FireChaserSyncController(composer: $mockedComposer))->__invoke($request);

    expect($response->status())->toBe(500)
        ->and($response->content())->toBe('There was an error fetching composer packages.');
});

it('filters out invalid packages', function () use ($key) {
    $mockedComposer = Mockery::mock(InstalledVersions::class);
    $mockedComposer->shouldReceive('getAllRawData')
        ->andReturn([
            [
                'versions' => [
                    'grayloon/firechaser-laravel' => [
                        // missing 'version' key
                    ]
                ]
            ]
        ]);

    $request = Request::create(
        uri: route('firechaser.sync') . '?api_site_key=' . $key,
    );

    $response = (new FireChaserSyncController(composer: $mockedComposer))->__invoke($request);

    expect($response->status())->toBe(500)
        ->and($response->content())->toBe('There was an error fetching composer packages.');
});

it('successful sync', function () use ($key) {
    $mockedComposer = Mockery::mock(InstalledVersions::class);
    $mockedComposer->shouldReceive('getAllRawData')
        ->andReturn([
            [
                'versions' => [
                    'grayloon/firechaser-laravel' => [
                        'version' => '1.0.0'
                    ]
                ]
            ]
        ]);

    $request = Request::create(
        uri: route('firechaser.sync') . '?api_site_key=' . $key,
    );

    $response = (new FireChaserSyncController(composer: $mockedComposer))->__invoke($request);

    expect($response->status())->toBe(200)
        ->and($response->getData(true))->toHaveKeys(['php', 'packages'])
        ->and($response->getData(true)['packages'])->toHaveKeys(['grayloon/firechaser-laravel'])
        ->and($response->getData(true)['packages']['grayloon/firechaser-laravel'])->toHaveKeys(['version'])
        ->and($response->getData(true)['packages']['grayloon/firechaser-laravel']['version'])->toBe('1.0.0');
});
