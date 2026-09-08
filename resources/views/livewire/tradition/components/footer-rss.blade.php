{{--
    footer RSS 订阅链接分部（footer.blade.php 经 @include 引用，与 footer 同主题目录；
    无可用流时由调用方不渲染）
    接收：$feeds（array[]，每项含 url / title / label），逐流渲染一条入口
--}}

@foreach ($feeds as $feed)
    <a href="{{ $feed['url'] }}" title="{{ $feed['title'] }}" class="sn-content-text hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm inline-flex items-center gap-1.5 px-3 {{ ! $loop->last ? 'border-r border-gray-200 dark:border-gray-700' : '' }}">
        <svg class="w-3.5 h-3.5 text-orange-500 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.18 20.82a2.18 2.18 0 1 1 0-4.36 2.18 2.18 0 0 1 0 4.36zM4 4.44v3.2c6.83 0 12.36 5.53 12.36 12.36h3.2C19.56 11.4 12.6 4.44 4 4.44zm0 5.66v3.2c3.87 0 7 3.13 7 7h3.2c0-5.63-4.57-10.2-10.2-10.2z"/></svg>
        {{ $feed['label'] }}
    </a>
@endforeach
