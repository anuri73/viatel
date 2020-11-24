<?php

class Asset
{
    private $viatel;

    public function __construct(Viatel $viatel)
    {
        $this->viatel = $viatel;
    }

    public function get_assets_dir()
    {
        return $this->viatel->config->get_dir(implode(DIRECTORY_SEPARATOR, [
            'asset',
            'dist',
        ]));
    }

    public function get_asset_path($asset)
    {
        return plugins_url(implode(DIRECTORY_SEPARATOR, [
            'viatel',
            'asset',
            'dist',
            $asset,
        ]));
    }

    public function get_asset_name($name)
    {
        return implode('-', array_filter([
            $this->viatel->config->get_core_slug(),
            $name,
        ]));
    }
}
