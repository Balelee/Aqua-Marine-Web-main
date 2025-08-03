@extends('admin.layout.app')

<style>
    .material-icons {
        margin-top: 0px !important;
        margin-bottom: 0px !important;
    }

    .a {
        width: 500px !important;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: scroll;
    }
</style>

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
            <div class="alert alert-success">
                @if(is_array(session()->get('success')))
                <ul>
                    @foreach (session()->get('success') as $message)
                    <li>{{ $message }}</li>
                    @endforeach
                </ul>
                @else
                {{ session()->get('success') }}
                @endif
            </div>
            @endif
            @if (count($errors) > 0)
            @if($errors->any())
            <div class="alert alert-danger" role="alert">
                {{$errors->first()}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            @endif
            @endif
        </div>
        <div class="col-lg-12">
            <br>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <div class="row">
                        <div class="col-md-4">
                            <h1 class="card-title"><b>{{ __('keywords.Stories') }} {{ __('keywords.List') }}</b></h1>
                        </div>
                        <div class="col-md-8">
                            <a href="{{ route('delete_all_stories') }}"
                               onClick="return confirm('Are you sure you want to permanently remove all Stories?')"
                               rel="tooltip" class="btn btn-danger p-1 ml-auto" style="float:right;">
                                <i class="fa fa-trash"></i> {{ __('keywords.All') }}
                            </a> &nbsp; &nbsp;
                        </div>
                    </div>
                </div>
                <div class="container"><br>
                    <table id="datatableDefault" class="table table-responsive table-striped text-nowrap w-100">
                        <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width:10% !important">#</th>
                            <th class="text-center" style="width:25% !important">{{ __('keywords.Title') }}</th>
                            <th class="text-center" style="width:20% !important">{{ __('keywords.Content') }}</th>
                            <th class="text-center" style="width:25% !important">{{ __('keywords.Text') }}</th>
                            <th class="text-center" style="width:20% !important">{{ __('keywords.Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($stories) > 0)
                        @php $i = 1; @endphp
                        @foreach($stories as $story)
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td>{{ $story->title }}</td>
                            <td>
                                @if($story->type === 'image' && $story->image)
                                <img src="{{ $url_aws . $story->image }}"
                                     alt="story image"
                                     style="width:50px; height:50px; border-radius:50%;" />
                                @elseif($story->type === 'video' && $story->video)
                                <video width="100" height="50" controls>
                                    <source src="{{ $url_aws . $story->video }}" type="video/mp4">
                                    {{ __('keywords.Video not supported') }}
                                </video>
                                @else
                                <p style="color:red"><b>{{ __('keywords.No Content') }}</b></p>
                                @endif
                            </td>
                            <td class="truncate">
                                <span class="a">{{ $story->text }}</span>
                            </td>
                            <td>
                                <a href="{{ route('edit_story', $story->id) }}"
                                   class="btn btn-primary btn-sm">
                                    {{ __('keywords.Edit') }}
                                </a>
                                <a href="{{ route('delete_story', $story->id) }}"
                                   onClick="return confirm('Are you sure you want to delete this story?')"
                                   class="btn btn-danger btn-sm">
                                    {{ __('keywords.Delete') }}
                                </a>
                            </td>
                        </tr>
                        @php $i++; @endphp
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5" class="text-center">{{ __('keywords.No data found') }}</td>
                        </tr>
                        @endif
                        </tbody>
                    </table>
                    <div class="pull-right mb-1" style="float: right;">
                        {{ $stories->render("pagination::bootstrap-4") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
