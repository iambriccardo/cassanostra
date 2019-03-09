<? if (session_status() == PHP_SESSION_ACTIVE && $_SESSION["role"] === "MAG"): ?>

    <div class="row">
        <div class="col s12 m12 l6 center offset-l3">
            <div class="card grey lighten-5">
                <div class="card-content black-text">
                    <span class="card-title">Inserimento prodotti nel magazzino</span>
                    <div class="row">
                        <div class="input-field col s12">
                            <select>
                                <option value="" disabled selected>Scegli il punto vendita dove vuoi operare</option>
                                <option value="1">Spini di Gardolo</option>
                                <option value="2">Montevaccino</option>
                                <option value="3">Canova</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12 m12 l6 center offset-l3">
            <table class="striped highlight responsive-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Marca</th>
                        <th>Prezzo</th>
                        <th>Quantita'</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Alvin</td>
                        <td>Eclair</td>
                        <td>$0.87</td>
                        <td>2</td>
                    </tr>
                    <tr>
                        <td>Alan</td>
                        <td>Jellybean</td>
                        <td>$3.76</td>
                        <td>4</td>
                    </tr>
                    <tr>
                        <td>Jonathan</td>
                        <td>Lollipop</td>
                        <td>$7.00</td>
                        <td>7 miliardi</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="fixed-action-btn">
        <a class="btn-floating btn-large blue-grey darken-4">
            <i class="large material-icons">add</i>
        </a>
        <ul>
            <li><a class="btn-floating red"><i class="material-icons">insert_chart</i></a></li>
            <li><a class="btn-floating yellow darken-1"><i class="material-icons">format_quote</i></a></li>
            <li><a class="btn-floating green"><i class="material-icons">publish</i></a></li>
            <li><a class="btn-floating blue"><i class="material-icons">attach_file</i></a></li>
        </ul>
    </div>


    <script>
        $(document).ready(function() {
            $('select').formSelect();
        });

    </script>
    <script>
        $(document).ready(function() {
            $('.fixed-action-btn').floatingActionButton();
        });
    </script>

<? endif; ?>