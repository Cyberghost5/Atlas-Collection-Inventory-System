<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price Tag Barcodes & QR Labels - Atlas Collection</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Outfit:wght@800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- QRCode.js CDN for Vector QR Rendering -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; margin: 0 !important; }
            .page-break { page-break-after: always !important; }
            .sticker-card { border: 1px solid #e2e8f0 !important; box-shadow: none !important; break-inside: avoid !important; }
        }

        /* Standard 80mm Thermal Sticker Width */
        .thermal-label {
            width: 78mm;
            padding: 4mm;
        }

        /* Standard A4 Sticker Label Dimensions */
        .a4-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6mm;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-900 p-4 sm:p-8 font-sans">

    <!-- Top Action Bar Controls -->
    <div class="no-print max-w-5xl mx-auto mb-6 bg-slate-900 text-white rounded-2xl p-4 shadow-xl flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('products.index') }}" class="text-xs font-bold text-slate-300 hover:text-white flex items-center space-x-1">
                <span>&larr; Back to Inventory</span>
            </a>
            <span class="text-slate-600">|</span>
            <span class="text-xs font-bold text-amber-400 font-mono">🏷️ Price Tag Sticker Generator ({{ count($labels) }} Label Units)</span>
        </div>

        <div class="flex items-center space-x-3">
            <!-- Format Switcher -->
            @if(isset($isBulk) && $isBulk)
                <span class="text-xs font-extrabold text-amber-400">Bulk Sheet Printing</span>
            @else
                <a href="{{ route('products.barcode', ['product' => $product, 'format' => 'a4', 'count' => $count]) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $format === 'a4' ? 'bg-amber-500 text-slate-950 shadow' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    📄 A4 Sticker Sheet Grid (3×8)
                </a>
                <a href="{{ route('products.barcode', ['product' => $product, 'format' => 'thermal', 'count' => $count]) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $format === 'thermal' ? 'bg-amber-500 text-slate-950 shadow' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    🧾 80mm POS Thermal Roll
                </a>
            @endif

            <!-- Print Button -->
            <button onclick="window.print()" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs rounded-xl shadow transition-all flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Print Price Tag Labels</span>
            </button>
        </div>
    </div>

    <!-- Printable Content Sheet Container -->
    <div class="max-w-5xl mx-auto">

        @if($format === 'thermal')
            <!-- 80mm Thermal Sticker Roll Output -->
            <div class="flex flex-col items-center space-y-6">
                @foreach($labels as $index => $item)
                    <div class="sticker-card thermal-label bg-white border border-slate-300 rounded-2xl shadow-md p-4 text-center space-y-2 relative overflow-hidden">
                        <!-- Branding Header -->
                        <div class="border-b border-slate-200 pb-1.5 flex items-center justify-between">
                            <span class="text-[9px] font-black tracking-wider uppercase text-slate-900 font-display">ATLAS COLLECTION</span>
                            <span class="text-[8px] font-bold text-slate-500 uppercase">BAUCHI, NG</span>
                        </div>

                        <!-- Product Name & Variant -->
                        <div>
                            <h4 class="text-xs font-black text-slate-900 leading-tight truncate" title="{{ $item['product']->name }}">{{ $item['product']->name }}</h4>
                            <p class="text-[10px] text-slate-600 font-medium">
                                {{ $item['product']->category->name ?? 'Catalog Item' }}
                                @if($item['product']->size) • <span class="font-bold text-amber-700">Size: {{ $item['product']->size }}</span> @endif
                            </p>
                        </div>

                        <!-- Price Tag Badge -->
                        <div class="py-1 bg-slate-950 text-amber-400 rounded-xl font-black font-display text-sm tracking-tight">
                            ₦{{ number_format($item['product']->selling_price, 2) }}
                        </div>

                        <!-- Barcode & QR Code Flex Layout -->
                        <div class="pt-1 flex items-center justify-between gap-2 border-t border-slate-100">
                            <!-- 1D Barcode Vector SVG -->
                            <div class="flex-1 overflow-hidden flex flex-col items-center justify-center">
                                <div class="w-full flex justify-center">
                                    {!! $item['barcodeSvg'] !!}
                                </div>
                                <span class="text-[9px] font-mono font-bold text-slate-800 tracking-wider mt-0.5">SKU: {{ $item['product']->sku }}</span>
                            </div>

                            <!-- QR Code Canvas Container -->
                            <div class="flex flex-col items-center flex-shrink-0">
                                <div id="qrcode-thermal-{{ $index }}" class="p-1 bg-white border border-slate-200 rounded-lg"></div>
                                <span class="text-[7px] text-slate-400 font-bold uppercase tracking-tighter mt-0.5">SCAN ITEM</span>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                new QRCode(document.getElementById("qrcode-thermal-{{ $index }}"), {
                                    text: "{{ $item['productUrl'] }}",
                                    width: 44,
                                    height: 44,
                                    colorDark : "#000000",
                                    colorLight : "#ffffff",
                                    correctLevel : QRCode.CorrectLevel.M
                                });
                            });
                        </script>
                    </div>
                @endforeach
            </div>

        @else
            <!-- Standard A4 Sticker Sheet Grid Layout (3 Columns x 8 Rows = 24 Labels per Page) -->
            @php $chunks = array_chunk($labels, 24); @endphp

            @foreach($chunks as $pageIndex => $chunkLabels)
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-200 mb-8 {{ !$loop->last ? 'page-break' : '' }}">
                    <div class="no-print border-b border-slate-200 pb-3 mb-6 flex justify-between items-center text-xs text-slate-500">
                        <span class="font-bold text-slate-900">A4 Printable Sticker Grid Sheet (Page {{ $pageIndex + 1 }} of {{ count($chunks) }})</span>
                        <span>24 Labels per Page Grid • 3×8 Columns</span>
                    </div>

                    <div class="a4-grid">
                        @foreach($chunkLabels as $itemIndex => $item)
                            @php $globalIdx = ($pageIndex * 24) + $itemIndex; @endphp
                            <div class="sticker-card bg-white border border-slate-300 rounded-2xl p-3.5 text-center space-y-2 flex flex-col justify-between relative">
                                <!-- Header -->
                                <div class="border-b border-slate-200 pb-1 flex items-center justify-between">
                                    <span class="text-[8px] font-black tracking-wider uppercase text-slate-900 font-display">ATLAS COLLECTION</span>
                                    <span class="text-[7px] font-bold text-slate-400">BAUCHI</span>
                                </div>

                                <!-- Product Info -->
                                <div>
                                    <h4 class="text-[11px] font-black text-slate-900 leading-tight truncate" title="{{ $item['product']->name }}">{{ $item['product']->name }}</h4>
                                    <p class="text-[9px] text-slate-600 font-semibold truncate">
                                        {{ $item['product']->category->name ?? 'Item' }}
                                        @if($item['product']->size) • <span class="text-amber-800 font-bold">Size: {{ $item['product']->size }}</span> @endif
                                    </p>
                                </div>

                                <!-- Price Badge -->
                                <div class="py-1 bg-slate-950 text-amber-400 rounded-xl font-black font-display text-xs tracking-tight">
                                    ₦{{ number_format($item['product']->selling_price, 2) }}
                                </div>

                                <!-- Barcode & QR Code Section -->
                                <div class="pt-1 flex items-center justify-between gap-1.5 border-t border-slate-100">
                                    <div class="flex-1 overflow-hidden flex flex-col items-center justify-center">
                                        <div class="w-full flex justify-center">
                                            {!! $item['barcodeSvg'] !!}
                                        </div>
                                        <span class="text-[8px] font-mono font-bold text-slate-900 tracking-wider mt-0.5">SKU: {{ $item['product']->sku }}</span>
                                    </div>

                                    <div class="flex flex-col items-center flex-shrink-0">
                                        <div id="qrcode-a4-{{ $globalIdx }}" class="p-1 bg-white border border-slate-200 rounded-lg"></div>
                                        <span class="text-[6px] text-slate-400 font-bold tracking-tighter mt-0.5">SCAN</span>
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        new QRCode(document.getElementById("qrcode-a4-{{ $globalIdx }}"), {
                                            text: "{{ $item['productUrl'] }}",
                                            width: 40,
                                            height: 40,
                                            colorDark : "#000000",
                                            colorLight : "#ffffff",
                                            correctLevel : QRCode.CorrectLevel.M
                                        });
                                    });
                                </script>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif

    </div>

</body>
</html>
