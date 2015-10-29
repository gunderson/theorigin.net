<html>
<body>
<form method="post" action="../process.php">
	<table class="formTable">
		<tr>
			<td colspan="2"><h1>Login</h1>
			</td>
		</tr>
		<tr>
			<td>Username:
			</td>
			<td><input name="username" type="text" id="username" />
			</td>
		</tr>
		<tr>
			<td>Password:
			</td>
			<td><input name="password" type="password" id="password" />
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
            <input name="do" type="hidden" id="do" value="login" />
			<input type="submit" id="submit" value="submit" />
			</td>
		</tr>
	</table>
</form>
</body>
</html>
