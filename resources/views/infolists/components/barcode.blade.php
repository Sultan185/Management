<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div id="barcode">
        {{ $getState() }}
        <img
        src="data:image/png;base64,{{DNS1D::getBarcodePNG($getRecord()->barcode_number, 'EAN13')}}"
        alt="barcode"
        style="height:50px;width:100px"
        />
    <p class="text-xs mt-0">{{$getRecord()->barcode_number}}</p>
    </div>
</x-dynamic-component>
