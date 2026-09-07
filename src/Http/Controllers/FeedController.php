<?php

namespace Wsmallnews\Cms\Http\Controllers;

use Illuminate\Http\Response;
use Wsmallnews\Cms\Settings\GeneralSettings;
use Wsmallnews\Cms\Support\Utils;

/**
 * RSS 订阅（RSS 2.0）：输出当前 scope 的已发布文章。
 * 注册在站点根路径（不参与 cms 前缀），站点标题/描述取站点设置。
 */
class FeedController
{
    public function __invoke(): Response
    {
        $general = app(GeneralSettings::class);

        $siteName = filled($general->site_name) ? $general->site_name : config('app.name');
        $description = filled($general->seo_description) ? $general->seo_description : $general->site_slogan;
        $siteUrl = Utils::route('index');

        $posts = Utils::getPostModel()::snScope(...Utils::getScopeable())
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit((int) Utils::getConfig('feed.limit', 50))
            ->get(['id', 'slug', 'title', 'description', 'published_at']);

        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<rss version="2.0">',
            '    <channel>',
            '        <title>' . e($siteName) . '</title>',
            '        <link>' . e($siteUrl) . '</link>',
            '        <description>' . e((string) $description) . '</description>',
            '        <language>' . str_replace('_', '-', app()->getLocale()) . '</language>',
            '        <lastBuildDate>' . now()->toRfc2822String() . '</lastBuildDate>',
        ];

        foreach ($posts as $post) {
            $url = Utils::route('posts.show', $post);

            $lines[] = '        <item>';
            $lines[] = '            <title>' . e($post->title ?? '') . '</title>';
            $lines[] = '            <link>' . e($url) . '</link>';
            $lines[] = '            <guid>' . e($url) . '</guid>';
            if (filled($post->description)) {
                $lines[] = '            <description>' . e($post->description) . '</description>';
            }
            if ($post->published_at) {
                $lines[] = '            <pubDate>' . $post->published_at->toRfc2822String() . '</pubDate>';
            }
            $lines[] = '        </item>';
        }

        $lines[] = '    </channel>';
        $lines[] = '</rss>';

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
        ]);
    }
}
