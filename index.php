<!DOCTYPE html>
<html lang="pt-br">
<?php include('layout/header.php') ?>

<body>
    <main class="container-fluid align-items-center justify-content-center min-vh-100 d-flex flex-column overflow-hidden gap-4">
        <section class="w-75 d-flex flex-column align-items-center">
            <header class="mb-5">
                <h1 class="h1 text-uppercase text-center fw-bold display-4 lh-base">Descubra Seu <span class="highlight">Signo</span><br>em Segundos</h1>
                <p class="text-center fw-light text-black-50">Informe sua data de nascimento e revele os mistérios do zodíaco.<br>Simples, rápido e gratuito!</p>
            </header>

            <form action="show_zodiac_sign.php" method="post" class="input-group p-2 border border-2 border-dark rounded gap-4 bg-input align-items-center">
                <input
                    type="date"
                    name="birth"
                    id="birth"
                    required
                    class="form-control border-0 bg-transparent rounded"
                    min="1900-01-01"
                    max="<?php echo date('Y-m-d'); ?>">
                <button type="submit" class="btn rounded">Enviar</button>
            </form>

        </section>
        <?php include('layout/footer.php') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>