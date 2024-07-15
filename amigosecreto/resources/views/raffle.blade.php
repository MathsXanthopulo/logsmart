@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Sorteio de Amigo Secreto</h1>

        @if ($pairs->isEmpty())
            <p>Nenhum par encontrado para o sorteio.</p>
        @else
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Presenteia</th>
                        <th>Recebedor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pairs as $pair)
                        <tr>
                            <td>{{ $pair->giver->name }}</td>
                            <td>{{ $pair->receiver->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Voltar para Home</a>
    </div>
@endsection
