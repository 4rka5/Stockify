<?php

namespace App\View\Composers;

use App\Models\AppSetting;
use Illuminate\View\View;

class AppSettingComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $appName = AppSetting::get('app_name', 'Stockify');
        $appLogo = AppSetting::get('app_logo');

        $view->with([
            'appName' => $appName,
            'appLogo' => $appLogo
        ]);
    }
}
