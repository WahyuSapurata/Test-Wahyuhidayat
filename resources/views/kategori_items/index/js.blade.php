<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>

<script>
    var start_date = '';
    var end_date = '';
    var data_per_fetch = 500;
    var data_fetched = 0;

    $(document).ready(function() {
        $('#table').DataTable({
            searching: false,
            order: [
                [0, 'desc']
            ],
        });
        getData()
    });

    $('.btn-get-data').click(function() {
        getData()
    })

    function getData() {

        $('#loading-filter').show();

        let dataTableObj = $('#table').DataTable();
        let filter_kode = $('#filter-kode').val();
        let filter_nama = $('#filter-nama').val();

        dataTableObj.clear().draw();

        $.ajax({
            url: '{{ url('kategori-items/search') }}',
            dataType: 'json',
            data: {
                kode: filter_kode,
                nama: filter_nama
            },
            success: function(results) {

                $.each(results.data, function(index, item) {

                    var kode = item.kode;
                    let html =
                        `<a href="{{ url('kategori-items/view') }}/${kode}" class="btn btn-primary">View</a> <a href="{{ url('kategori-items/print') }}/${kode}" class="btn btn-danger" target="_blank">
    Print PDF
</a>`;

                    dataTableObj.row.add([
                        item.kode,
                        item.nama,
                        html
                    ]).draw(false);

                });

                $('#loading-filter').hide();
            },
            error: function() {
                alert('Terjadi kesalahan server, tidak dapat mengambil data');
                $('#loading-filter').hide();
            }
        });
    }
</script>
