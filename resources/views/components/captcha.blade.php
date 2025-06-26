<div class="form-group mb-3">
    <label for="captcha">Mã xác thực <span class="text-danger">*</span></label>
    <div class="captcha-container">
        <span id="captcha-img">{!! Captcha::img('default') !!}</span>
        <button type="button" class="btn btn-primary" id="refresh-captcha">
            <i class="fa fa-refresh"></i> Làm mới mã
        </button>
    </div>
    <input id="captcha" type="text" class="form-control mt-2 @error('captcha') is-invalid @enderror" 
           name="captcha" placeholder="Nhập mã xác thực">
    @error('captcha')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>

@once
    @push('scripts')
    <script>
    $(document).ready(function() {
        $('#refresh-captcha').click(function() {
            $.ajax({
                url: '{{ route("reload.captcha") }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#captcha-img').html(data.captcha);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + ' - ' + error);
                }
            });
        });
    });
    </script>
    @endpush
@endonce
