      <hr>
      <footer>
      <span>&copy; <a href='index.php'><?php echo APP_NAME ."</a> ";
      echo date('Y'); ?></span>
        <p class="pull-right">
          <?php
            $time = explode(' ',microtime());
            $finish = $time[1] + $time[0];
            $total = round(($finish - $start), 4);
            echo "Page generated in ".$total." seconds.";
          ?>
        </p>
        <?php 
        echo "<p>";
        if(DEBUG === true) {
          echo "<div class='row'><div class='col-md-4'>GET";
          var_dump($_GET);
          echo "</div><div class='col-md-4'>POST";
          var_dump($_POST);
          echo "</div><div class='col-md-4'>SESSION";
          var_dump($_SESSION);
          echo "</div></div>";
        }
        echo "</p>";
        var_dump($_SERVER);
        ?>
      </footer>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="assets/js/vendor/moment.js"></script>
    <script src="assets/js/vendor/datetimepicker.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.17.7/js/jquery.tablesorter.min.js"></script>
    <script src="assets/js/icons.js"></script>
    <script src="assets/js/app.js"></script>
  </body>
</html>