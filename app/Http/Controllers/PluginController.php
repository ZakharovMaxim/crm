<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plugin;
use Illuminate\Support\Facades\DB;

class PluginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Plugin::where(['is_deleted' => 0])->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $plugin = Plugin::create($request->all());
        $plugin['enabled'] = true;
        $plugin->set_settings()->attach($request->input('settings'));
        return $plugin;
    }
    /**
     * Update Settings
     *
     * @param Plugin  $plugin
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update_settings(Plugin $plugin, Request $request)
    {
        // return $request->input('settings');
        $res = [];
        foreach($request->input('settings') as $setting)
        {
            $res[$setting['name']] = ['value' => $setting['value']];
        }
        // return $res;
        $plugin->set_settings()->sync($res);
        return $plugin;
    }
    public function set_state(Plugin $plugin, Request $request)
    {
        $plugin->enabled = $request->input('enabled');
        $plugin->save();
        return 'ok';
    }
    /**
     * Display the specified resource.
     *
     * @param  Plugin $plugin
     * @return \Illuminate\Http\Response
     */
    public function show(Plugin $plugin)
    {
        if ($plugin->is_deleted) {
            return abort(404);
        }
        $plugin->settings;
        return $plugin;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Plugin $plugin
     * @return \Illuminate\Http\Response
     */
    public function edit(Plugin $plugin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Plugin $plugin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plugin $plugin)
    {
        $plugin->update($request->all());
        return 'ok';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Plugin $plugin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plugin $plugin)
    {
        $plugin->is_deleted = true;
        $plugin->save();
    }

    static public function isPluginEnabledByKey($key)
    {
        $settings = DB::table('plugins_settings')->where(['name' => 'api_key', 'value' => $key])->get();
        if (!isset($settings[0])) return false;
        $plugin = Plugin::find($settings[0]->plugin_id);
        return !$plugin->is_deleted && $plugin->enabled;
    }
}
