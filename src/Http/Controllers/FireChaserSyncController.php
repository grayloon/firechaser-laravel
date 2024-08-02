<?php

namespace GrayLoon\FireChaser\Http\Controllers;

use Composer\InstalledVersions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Request;


class FireChaserSyncController extends Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * @param  InstalledVersions|null  $composer
     */
    public function __construct(
        public ?InstalledVersions $composer = null
    ) {
        $this->composer = $composer ?? new InstalledVersions();
    }

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return mixed
     */
    public function __invoke(Request $request): mixed
    {
        if (
            config('app.env') === 'production'
            && config('firechaser.api_site_key')
            && $request->get('apiKey')
        ) {
            if (config('firechaser.api_site_key') === $request->get('apiKey')) {
                // Resolve current applications vendor packages.
                $vendors = $this->composer?->getAllRawData();

                if (\count($vendors) > 0 && isset($vendors[0]['versions'])) {
                    // Loop through all returned vendor packages and filter out "replaced" or incomplete packages.
                    $packages = collect($vendors[0]['versions'])
                        ->filter(fn ($vendor, $key) => isset($vendor['version']))
                        ->toArray();

                    if (count($packages) > 0) {
                        return response()->json([
                            'php'      => phpversion(),
                            'packages' => $packages,
                        ]);
                    }
                }

                return response('There was an error fetching composer packages.', status: 500);
            }

            return response('Invalid API Site Key given.', status: 403);
        }

        return response('Application not in production or missing API Site Key.', status: 500);
    }
}
