@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card mb-4 p-2">
                <ul class="nav nav-pills nav-justified">
                    <li class="nav-item dropdown">
                        <select class="form-control" id="year">
                            @for ($i = 2018; $i <= date('Y')+1; $i++)
                                @if ($selectedYear == $i)
                                    <option selected="selected">{{ $i }}</option>
                                @else
                                    <option>{{ $i }}</option>
                                @endif
                            @endfor
                        </select>
                    </li>
                    @for ($i = 1; $i <= 12; $i++)
                        <li class="nav-item">
                            @if ($selectedMonth == $i)
                                <a class="nav-link active" href="{{ url('?month='.$i.'&year='.$selectedYear) }}">{{ $monthList[$i] }}</a>
                            @else
                                <a class="nav-link" href="{{ url('?month='.$i.'&year='.$selectedYear) }}">{{ $monthList[$i] }}</a>
                            @endif
                        </li>
                    @endfor
                </ul>
            </div>

            <div class="alert alert-dark d-flex justify-content-between align-items-center">
                <h4>{{ $monthName . '/' . $selectedYear }}</h4>
                <span>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMovement">Adicionar movimento</button>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCategory">Nova categoria</button>
                </span>
            </div>

            <div class="card-deck mb-4">
                <div class="card">
                    <div class="card-header font-weight-bold">Receitas e despesas do mês</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center text-success">
                                <span>Receitas</span>
                                <span>{{ Money::money($monthlyIncomes, true) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center text-danger">
                                <span>Despesas</span>
                                <span>{{ Money::money($monthlyExpenses, true) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center font-weight-bold">
                                <span>Total</span>
                                <span>{{ Money::money($monthlyTotal, true) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header font-weight-bold">Balanço Anual</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center text-success">
                                <span>Receitas</span>
                                <span>{{ Money::money($annualIncomes, true) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center text-danger">
                                <span>Despesas</span>
                                <span>{{ Money::money($annualExpenses, true) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center font-weight-bold">
                                <span>Total</span>
                                <span>{{ Money::money($annualTotal, true) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header font-weight-bold">Movimentação do mês</div>
                <div class="card-body">
                    <table class="table table-hover table-striped table-sm">
                        <tbody>
                            @foreach ($monthlyMovements as $movement)
                                <tr>
                                    <td style="width: 50px;">{{ $movement->date->format('d') }}</td>
                                    <td>{{ '(' . $movement->category->name . ') ' . $movement->description }} <i class="fas fa-edit"></i> <i class="fas fa-trash-alt"></i></td>
                                    @if ($movement->type === 'D')
                                        <td class="text-right text-danger">-{{ Money::money($movement->value, true) }}</td>
                                    @else
                                        <td class="text-right text-success">+{{ Money::money($movement->value, true) }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2"><h4>Total</h4></th>
                                <th class="text-right"><h4>{{ Money::money($monthlyTotal, true) }}</h4></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header font-weight-bold">Receitas x Despesas (Anual)</div>
                <div class="card-body">
                    <canvas id="canvas" height="60"></canvas>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modals -->

<div class="modal fade" id="addMovement" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post" action="{{ url('movement') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar movimento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group custom-control custom-radio custom-control-inline">
                            <input type="radio" checked="checked" id="type_receita" name="type" class="custom-control-input" value="R">
                            <label class="custom-control-label" for="type_receita">Receita</label>
                        </div>
                        <div class="form-group custom-control custom-radio custom-control-inline">
                            <input type="radio" id="type_despesa" name="type" class="custom-control-input" value="D">
                            <label class="custom-control-label" for="type_despesa">Despesa</label>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="category">Categoria</label>
                            <select id="category" class="form-control" name="category">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="description">Descrição</label>
                            <input type="text" class="form-control" id="description" name="description">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="date">Data</label>
                            <input type="text" class="form-control" id="date" name="date" value="{{ date('d/m/Y') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="value">Valor</label>
                            <input type="text" class="form-control" id="value" name="value">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post" action="{{ url('category') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nova categoria</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="name">Nome</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#year').change(function() {
            window.location.href = "{!! url('?month='.$selectedMonth.'&year=') !!}" + $(this).val();
        });

        var ctx = document.getElementById("canvas").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! $chart['labels']->toJson() !!},
                datasets: [
                    {
                        label: 'Receitas',
                        fill: false,
                        data: {!! $chart['incomes']->toJson() !!},
                        borderWidth: 2,
                        borderColor: [
                            'green'
                        ],
                    },
                    {
                        label: 'Despesas',
                        fill: false,
                        data: {!! $chart['expenses']->toJson() !!},
                        borderWidth: 2,
                        borderColor: [
                            'red'
                        ],
                    }
                ],
                options: {
					responsive: true,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            }
        });

    });
</script>

@endsection
