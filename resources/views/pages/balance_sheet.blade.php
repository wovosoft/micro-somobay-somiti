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

        table.datatable,
        table.datatable tr,
        table.datatable td,
        table.datatable th {
            border: 1px solid gray;
            border-collapse: collapse;
        }

        table.datatable td {
            padding: 5px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .my-0 {
            margin-top: 0;
            margin-bottom: 0;
        }

    </style>
</head>

<body>
    <div class="text-center">
        <h2 class="my-0">Dreamer's Association</h2>
        <h3 class="my-0">Members Balance Sheet</h3>
        @if (request()->input('starting_date'))
            From: {{ Carbon\Carbon::parse(request()->input('starting_date'))->format('d-m-Y') }}
        @endif
        @if (request()->input('ending_date'))
            To: {{ Carbon\Carbon::parse(request()->input('ending_date'))->format('d-m-Y') }}
        @endif
    </div>
    <table class="datatable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Membership No</th>
                <th>PF Index</th>
                <th>Current Workplace</th>
                <th>Total Deposit</th>
                <th>Total Withdraw</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->membership_no }}</td>
                    <td>{{ $item->pf_index }}</td>
                    <td>{{ $item->current_workplace }}</td>
                    <td class="text-right">{{ number_format($item->deposit_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($item->withdraw_amount, 2) }}</td>
                    <td class="text-right">
                        {{ number_format($item->deposit_amount - $item->withdraw_amount, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">Total</td>
                <td class="text-right">{{ number_format($items->sum('deposit_amount'), 2) }}</td>
                <td class="text-right">{{ number_format($items->sum('withdraw_amount'), 2) }}</td>
                <td class="text-right">
                    {{ number_format($items->sum('deposit_amount') - $items->sum('withdraw_amount'), 2) }}
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
