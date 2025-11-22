@if ($message = Session::get('error'))
    <div class="tt active">
        <div class="tt-content">
            <i class="fas fa-solid fa-exclamation exclamation update"></i>
            <div class="message">
                <span class="text text-1">Error</span>
                <span class="text text-2"> {{ $message }}</span>
            </div>
        </div>
        <i class="fa-solid fa-xmark close"></i>
        <div class="pg active"></div>
    </div>
@endif

@if ($message = Session::get('success'))
    <div class="tt active">
        <div class="tt-content">
            <i class="fas fa-solid fa-check check"></i>
            <div class="message">
                <span class="text text-1">Success</span>
                <span class="text text-2"> {{ $message }}</span>
            </div>
        </div>
        <i class="fa-solid fa-xmark close"></i>
        <div class="pg active"></div>
    </div>
@endif

@if ($message = Session::get('update'))
    <div class="tt active">
        <div class="tt-content">
            <i class="fas fa-solid fa-check check"></i>
            <div class="message">
                <span class="text text-1">Update</span>
                <span class="text text-2"> {{ $message }}</span>
            </div>
        </div>
        <i class="fa-solid fa-xmark close"></i>
        <div class="pg active"></div>
    </div>
@endif

@if ($message = Session::get('delete'))
    <div class="tt active">
        <div class="tt-content">
            <i class="fas fa-solid fa-exclamation exclamation update"></i>
            <div class="message">
                <span class="text text-1">Delete</span>
                <span class="text text-2"> {{ $message }}</span>
            </div>
        </div>
        <i class="fa-solid fa-xmark close"></i>
        <div class="pg active"></div>
    </div>
@endif


@if ($message = Session::get('info'))
    <div class="tt active">
        <div class="tt-content">
            <i class="fas fa-solid fa-exclamation exclamation update"></i>
            <div class="message">
                <span class="text text-1">Info</span>
                <span class="text text-2"> {{ $message }}</span>
            </div>
        </div>
        <i class="fa-solid fa-xmark close"></i>
        <div class="pg active"></div>
    </div>
@endif
