<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthorizeFileExtension
{
    use ApiResponser;

    private const AUTHORIZED_EXTENSIONS = [
        'avi', 'gif', 'jpg', 'jpeg', 'm4a',
        'ai', 'eps', 'png', 'tif', 'indd', 'svg',
        'mov', 'mp3', 'mp4', 'mpg', 'mpeg',
        'wav', 'wmv', 'xls', 'xlsx', 'csv',
        'xlt', 'xltm', 'xltx', 'xps',
        'doc', 'docm', 'docx',  'dot', 'dotx', 'pdf',
        'pot', 'potm', 'potx', 'ppam', 'pps', 'pptx',
        'ppt', 'ppsm', 'psd', 'ini', 'iso', 'pub',
        'rtf', 'sldm', 'sldx', 'txt',
    ];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure(Request): (Response|RedirectResponse)  $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $files = $request->allFiles();

        if (!empty($files)) {
            foreach ($files as $file) {
                if (is_executable($file->path())
                || !in_array($file->extension(), self::AUTHORIZED_EXTENSIONS)
                ) {
                    return $this->errorResponse(
                        __('This type of file is not authorized.'),
                        422
                    );
                }
            }
        }

        return $next($request);
    }
}
