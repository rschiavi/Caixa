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
                                @if ($selected_year == $i)
                                    <option selected="selected">{{ $i }}</option>
                                @else
                                    <option>{{ $i }}</option>
                                @endif
                            @endfor
                        </select>
                    </li>
                    @for ($i = 1; $i <= 12; $i++)
                        <li class="nav-item">
                            @if ($selected_month == $i)
                                <a class="nav-link active" href="{{ url('?month='.$i.'&year='.$selected_year) }}">{{ $months[$i] }}</a>
                            @else
                                <a class="nav-link" href="{{ url('?month='.$i.'&year='.$selected_year) }}">{{ $months[$i] }}</a>
                            @endif
                        </li>
                    @endfor
                </ul>
            </div>

            <div class="alert alert-dark d-flex justify-content-between align-items-center">
                <h4>{{ $month_name . '/' . $selected_year }}</h4>
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
                                <span>{{ Money::money($receitas, true) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center text-danger">
                                <span>Despesas</span>
                                <span>{{ Money::money($despesas, true) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center font-weight-bold">
                                <span>Total</span>
                                <span>{{ Money::money($total, true) }}</span>
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
                                <span>{{ Money::money($receitas, true) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center text-danger">
                                <span>Despesas</span>
                                <span>{{ Money::money($despesas, true) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center font-weight-bold">
                                <span>Total</span>
                                <span>{{ Money::money($total, true) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header font-weight-bold">Movimentação do mês</div>
                <div class="card-body">
                    <table class="table table-hover table-striped table-sm">
                        <tbody>
                            @foreach ($movements as $movement)
                                <tr>
                                    <td style="width: 50px;">{{ $movement->date->format('d') }}</td>
                                    <td>{{ '(' . $movement->category->name . ') ' . $movement->description }}</td>
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
                                <th class="text-right"><h4>{{ Money::money($total, true) }}</h4></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            {!! $chart->container() !!}


        </div>
    </div>
</div>

<!-- Modals -->

<div class="modal fade" id="addMovement" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar movimento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-row">
                        <div class="form-group custom-control custom-radio custom-control-inline">
                            <input type="radio" checked="checked" id="customRadioInline1" name="customRadioInline1" class="custom-control-input">
                            <label class="custom-control-label" for="customRadioInline1">Receita</label>
                        </div>
                        <div class="form-group custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline2" name="customRadioInline1" class="custom-control-input">
                            <label class="custom-control-label" for="customRadioInline2">Despesa</label>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="inputState">Categoria</label>
                            <select id="inputState" class="form-control">
                                @foreach ($categories as $category)
                                    <option>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="inputAddress">Descrição</label>
                            <input type="text" class="form-control" id="inputAddress">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">Data</label>
                            <input type="text" class="form-control" id="inputEmail4" value="{{ date('d/m/Y') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputPassword4">Valor</label>
                            <input type="text" class="form-control" id="inputPassword4">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova categoria</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputAddress">Nome</label>
                            <input type="text" class="form-control" id="inputAddress">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#year').change(function() {
            window.location.href = "{!! url('?month='.$selected_month.'&year=') !!}" + $(this).val();
        });
    });
</script>

@endsection
