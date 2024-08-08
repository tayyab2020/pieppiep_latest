@if (Session::has('success'))

      <div class="alert alert-success validation">
      <button type="button" class="close cl-btn" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <p style="color: #514c4c;" class="text-left">{!! Session::get('success') !!}</p>
      </div>

@endif

@if(Session::has('array_errors'))

      @foreach(Session::get('array_errors') as $key)

            <div class="alert alert-danger validation">
                  <button type="button" class="close cl-btn" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                  <p style="color: #514c4c;" class="text-left">{!! $key !!}</p>
            </div>

      @endforeach

@endif

@if (Session::has('unsuccess'))

      <div class="alert alert-danger validation">
      <button type="button" class="close cl-btn" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <p style="color: #514c4c;" class="text-left">{!! Session::get('unsuccess') !!}</p>
      </div>

@endif

@if(session('message')==='Failed!')
      <div class="alert alert-danger validation">
      <button type="button" class="close cl-btn" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <p>{{__('text.Credentials doesn\'t match')}}</p>
      </div>
@endif
