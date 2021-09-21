<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table {
            max-width: 1200px;
            width: 100%;
            margin: auto;
        }

        table.datatable, table.datatable tr, table.datatable td, table.datatable th {
            border: 1px solid gray;
            border-collapse: collapse;
        }

        table.datatable td {
            padding: 5px;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
<div style="text-align: center;max-width: 1200px;margin:auto;margin-bottom: 20px;">
    <h1 style="margin-bottom: 3px;">Dreamer's Association</h1>
    <span style="text-decoration: underline;">Expenses Report</span><br>
    Total: {{$items->count()}}

    @if(request()->input("type"))
        <h3>Type: {{\Illuminate\Support\Str::title(request()->input('type'))}}<br></h3>
    @endif
    @if(request()->input('starting_date') && request()->input('ending_date'))
        <h3>
            From : {{\Carbon\Carbon::parse(request()->input('starting_date'))->format('d-m-Y')}}
            To : {{\Carbon\Carbon::parse(request()->input('ending_date'))->format('d-m-Y')}}
        </h3>
    @elseif(request()->input('starting_date') || request()->input('ending_date'))
        <h3>
            Date
            : {{\Carbon\Carbon::parse(request()->input('starting_date') ?? request()->input('ending_date'))->format('d-m-Y')}}
        </h3>
    @endif
    <hr/>
</div>
<table class="datatable">
    <thead>
    <tr>
        <th>ID</th>
        <th>Date</th>
        @if(!request()->input('type'))
            <th>Type</th>
        @endif
        <th>Description</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            <td>{{$item->id}}</td>
            <td>{{\Illuminate\Support\Carbon::parse($item->date)->format("d-m-Y")}}</td>
            @if(!request()->input('type'))
                <td>{{\Illuminate\Support\Str::of($item->type)->title()}}</td>
            @endif
            <td>{{$item->description}}</td>
            <td class="text-right">{{number_format($item->amount,2)}}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3">Total</td>
        <td colspan="5" class="text-right">
            {{number_format($items->sum('amount'),2)}}
        </td>
    </tr>
    </tfoot>
</table>
</body>
</html>
