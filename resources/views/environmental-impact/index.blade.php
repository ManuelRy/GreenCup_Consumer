@extends('master')

@section('content')
<div class="container">
    <div class="responsive-table">
    <table>
        <div class="row">
            <p>All time</p>
        </div>
        <thead>
            <tr>
                <th>Total Cups Saved</th>
                <th>How Many Stores</th>
                <th>Total CO2 saved (cup x 20g)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{$total_cups}}</td>
                <td>{{$store_visited}}</td>
                <td> {{ $total_cups*9 }}g </td>
            </tr>
        </tbody>
    </table>
    </div>
</div>
@endsection
