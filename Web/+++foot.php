
    <!-- JAVA SCRIPT -->
    <script>
	    var ENV = {
		    SERVER:         <?= ENV::Server(); ?>,
		    SERVER_OFFLINE: 0,
		    SERVER_ONLINE:  1,

		    EXECUTION:         <?= ENV::Execution(); ?>,
		    EXECUTION_DEV:     0,
		    EXECUTION_STAGING: 1,
		    EXECUTION_PROD:    2,
	    };
    </script>
    <script src="<?= PATH::JS() ?>libs.js"></script>
    <script src="<?= PATH::JS() ?>app.js"></script>

</body>
</html>