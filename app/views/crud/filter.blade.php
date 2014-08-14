<div class="form-group" id="crud_filter_{{$model}}_{{$i}}">
  <div class="col-sm-1">{{$name}}</div>
  <div class="col-sm-2">
    <select class="form-control input-sm" name="filter[{{$i}}][{{$id}}][selector]">
       @foreach($selectors as $option)
       <option value="{{$option['id']}}">{{$option['name']}}</option>
       @endforeach
    </select>
  </div>  

  <div class="col-sm-6">
      @if($type == 'text')
      <input type="text" class="form-control input-sm" name="filter[{{$i}}][{{$id}}][data]">
      @elseif($type == 'number')
      <input type="text" class="form-control input-sm" name="filter[{{$i}}][{{$id}}][data]">
      @elseif($type=='select')
      <select  class="form-control input-sm" name="filter[{{$i}}][{{$id}}][data]">
       @foreach($resource as $r)
       <option value="{{$r['id']}}">{{$r['name']}}</option>
       @endforeach
      </select>
      @elseif($type=='date')
               <div class='datepicker input-group date'>
                    <input type='text' class="form-control" placeholder="dd/mm/yyyy" name="filter[{{$i}}][{{$id}}][data]" />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
      @endif
  </div>

  
  <div class="col-sm-1">
    <button class="crud_del_filter_btn btn btn-danger btn-sm" data-i="{{$i}}" data-model="{{$model}}">
      <span class="glyphicon glyphicon-remove"></span>
    </button>
  </div>
</div>

  