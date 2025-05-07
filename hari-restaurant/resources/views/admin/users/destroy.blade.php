@if (session('success'))
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
            Xóa người dùng thành công
        </div>
    @endif
@endif
