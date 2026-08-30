<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <!-- Homepage & Main Storefront -->
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ date('c') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('shop.index') }}</loc>
        <lastmod>{{ date('c') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ route('shop.categories') }}</loc>
        <lastmod>{{ date('c') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Dynamic Category Pages -->
    @foreach($categories as $category)
        <url>
            <loc>{{ route('shop.category.show', $category->slug) }}</loc>
            <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.85</priority>
        </url>
    @endforeach

    <!-- Dynamic Product Pages -->
    @foreach($products as $product)
        <url>
            <loc>{{ route('shop.show', $product->slug ?? $product->id) }}</loc>
            <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.9</priority>
            @if(!empty($product->image) && file_exists(public_path($product->image)))
                <image:image>
                    <image:loc>{{ asset($product->image) }}</image:loc>
                    <image:title>{{ htmlspecialchars($product->name) }}</image:title>
                </image:image>
            @endif
        </url>
    @endforeach
</urlset>
