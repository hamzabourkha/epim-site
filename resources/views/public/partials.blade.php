@php
if (! function_exists('epimHero')) {
    function epimHero($title, $text, $image = 'https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?auto=format&fit=crop&w=1600&q=80') {
        echo '<section class="page-hero" style="background-image:linear-gradient(90deg,rgba(0,75,156,.86),rgba(0,75,156,.45)),url('.e($image).')"><div class="container"><nav class="breadcrumb-lite">EPIM / '.e($title).'</nav><h1>'.e($title).'</h1><p>'.e($text).'</p></div></section>';
    }
}
@endphp
