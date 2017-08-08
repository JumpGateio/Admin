@section('title')
  {{ $title }}
  <div class="uk-button-group uk-float-right">
    @if (! empty($filtered))
      <a class="uk-button uk-button-default uk-button-small" href="{{ route($routes['index']) }}">
        <i class="fa fa-fw fa-ban"></i>&nbsp;Clear Filter
      </a>
    @endif
    @if (! empty($filters))
      <button class="uk-button uk-button-default uk-button-small" type="button" uk-toggle="target: #offcanvas-push">
        <i class="fa fa-fw fa-filter"></i>&nbsp;Filter
      </button>
    @endif
  </div>
  <a class="uk-button uk-button-primary uk-background-primary-light uk-text-white uk-button-small uk-float-right uk-margin-small-right" href="{{ route($routes['create']) }}">
    <i class="fa fa-fw fa-plus"></i>&nbsp;New
  </a>
@endsection
<table class="uk-table uk-table-divider uk-table-hover uk-table-small">
  <thead>
    <tr>
      @foreach ($columns as $column => $property)
        <th><a href="{{ route($routes['index'], ['orderBy' => $property]) }}">{{ $column }}</a></th>
      @endforeach
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($results as $result)
      <tr>
        @foreach ($columns as $column => $property)
          <td>{{ array_get($result, $property) }}</td>
        @endforeach
        <td class="uk-text-right">
          <a href="{{ route($routes['show'], $result['id']) }}" class="uk-button uk-button-small uk-button-primary-light uk-text-white">
            <i class="fa fa-fw fa-eye"></i>
          </a>
          <a href="{{ route($routes['edit'], $result['id']) }}" class="uk-button uk-button-small uk-button-primary-light uk-text-white">
            <i class="fa fa-fw fa-pencil"></i>
          </a>
          <a href="{{ route($routes['delete'], $result['id']) }}" data-method="delete" class="uk-button uk-button-small uk-button-danger">
            <i class="fa fa-fw fa-trash-o"></i>
          </a>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
{!! $results->appends(request()->except('page'))->links() !!}

@section('admin_filters')
  @if (! empty($filters))
    <div id="offcanvas-push" uk-offcanvas="mode: push; overlay: true; flip: true;">
      <div class="uk-offcanvas-bar">
        <button class="uk-offcanvas-close" type="button" uk-close></button>

        {!! Form::open(['route' => [$routes['index']], 'class' => 'uk-margin-small-top']) !!}

        @foreach ($filters as $property => $filter)
          <div class="uk-margin">
            @if (in_array($filter[0], ['text', 'number', 'email']))
              <input type="{{ $filter[0] }}" name="{{ $property }}" value="{{ array_get($filtered, $property, null) }}" class="uk-input" placeholder="{{ \Illuminate\Support\Str::title($property) }}" />
            @elseif ($filter[0] === 'select')
              <select name="{{ $property }}" class="uk-select">
                @foreach ($filter[1] as $value => $name)
                  <option value="{{ $value }}" {{ array_get($filtered, $property, null) == $value ? 'selected="selected"' : null }}>{{ $name }}</option>
                @endforeach
              </select>
            @endif
          </div>
        @endforeach

        <div class="uk-margin">
          <input type="submit" value="Filter" class="uk-button uk-button-primary uk-text-white uk-width-1-1" />
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  @endif
@show
