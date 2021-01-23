@extends('admin.layouts.app')

@section('title', 'Detail Formula')
@push('breadcrump')
<li><a href="{{route('formula.index')}}">Formula</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Formula</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
           <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('formula.update',['id'=>$formula->id])}}" class="form-horizontal" method="post"
          autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="box-body">
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Nama</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$formula->name}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="calculate" class="col-sm-2 control-label">Formula</label>
              <div class="col-sm-6">
                <table class="table table-bordered table-striped" id="table-formula">
                  <thead>
                      <th width="10" class="text-center">Operator</th>
                      <th width="250">Nilai</th>
                      <th width="10" class="text-center">Operator</th>
                  </thead>
                  <tbody>
                    @foreach ($formula->detail as $detail)
                        <tr>
                          <td class="text-center">{{$detail->operation_before}}</td>
                          <td>{{$detail->answer?$detail->answer->question->description.' - '.$detail->answer->description:$detail->value}}</td>
                          <td class="text-center">{{$detail->operation}}</td>
                        </tr>
                    @endforeach
                  </tbody>
                </table>
                <b><p class="form-control-static">{{$formula->calculate}} =</p></b>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="overlay hidden">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
  </div>
</div>
@endsection
