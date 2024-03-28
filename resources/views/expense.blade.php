<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Income - Outcome</title>

        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet"/>

        <!-- MDB -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet"/>

        <!--- Daterange Picker -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <style>
            body {
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #eee;
            }
        </style>
    </head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-end">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary" data-mdb-ripple-init data-mdb-modal-init data-mdb-target="#exampleModal">
                            Add New
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <ol class="list-group list-group-light list-group-numbered">
                        @foreach ($expenses as $expense)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                              <div class="fw-bold">{{ $expense->about }}</div>
                              {{ $expense->date }}
                            </div>
                            @if ($expense->type == 'income')
                            <span class="badge badge-success rounded-pill"><i class="fa-solid fa-plus me-1"></i>{{ $expense->amount }}</span>
                            @else
                            <span class="badge badge-danger rounded-pill"><i class="fa-solid fa-minus me-1"></i>{{ $expense->amount }}</span>
                            @endif
                        </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5>Today Chart</h5>
                        <div>
                            <span class="text-success me-3">ဝင်ငွေ : <i class="fa-solid fa-plus me-1"></i>{{ $todayTotalIncome }}</span>
                            <span class="text-danger">ထွက်ငွေ : <i class="fa-solid fa-minus me-1"></i>{{ $todayTotalOutcome }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Income or Outcome</h5>
                <button type="button" class="btn-close" data-mdb-ripple-init data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-outline mb-3" data-mdb-input-init>
                            <i class="fa-solid fa-dollar-sign trailing"></i>
                            <input type="text" class="form-control form-control-lg form-icon-trailing" name="amount" id="amount"/>
                            <label class="form-label" for="amount">Amount</label>
                        </div>
                        <div class="form-outline mb-3" data-mdb-input-init>
                            <textarea class="form-control" name="about" id="about" rows="2"></textarea>
                            <label class="form-label" for="about">About</label>
                        </div>
                        <div class="form-outline mb-3" data-mdb-input-init>
                            <i class="fa-regular fa-calendar-days trailing"></i>
                            <input type="text" class="form-control form-control-lg form-icon-trailing" name="date" id="date"/>
                            <label class="form-label" for="date">Date</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="income" value="income" checked/>
                            <label class="form-check-label" for="income">Income</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="outcome" value="outcome" />
                            <label class="form-check-label" for="outcome">Outcome</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-mdb-ripple-init data-mdb-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" data-mdb-ripple-init>Save</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- MDB -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.umd.min.js"></script>

<!--- Daterange Picker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<!--- Chart Js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!--- Sweetalert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
<script>
    $(document).ready(function(){

        @if (session('success'))
            Swal.fire({
                title: "Good job!",
                text: "{{ session('success') }}",
                icon: "success"
            });
        @endif

        $('#date').daterangepicker({
            "singleDatePicker": true,
            "autoApply": true,
            "locale": {
                "format": "YYYY-MM-DD",
            }
        });

        const ctx = document.getElementById('myChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
            labels: @json($dayArr),
            datasets: [
                {
                    label: 'ဝင်ငွေ',
                    data: @json($dailyIncome),
                    borderWidth: 1,
                    backgroundColor: '#14A44D',
                },
                {
                    label: 'ထွက်ငွေ',
                    data: @json($dailyOutcome),
                    borderWidth: 1,
                    backgroundColor: '#DC4C64',
                },
            ]
            },
            options: {
            scales: {
                y: {
                beginAtZero: true
                }
            }
            }
        });
    });
</script>
</html>
