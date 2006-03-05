{if !empty($smarty.session.userid)}
<!-- Begin javascript.tpl -->
<script language="javascript" type="text/javascript">
var timerID = null
var timerRunning = false
var sessionExpired = false
var startDate
var startSecs

function loader() {ldelim}
  startDate = new Date()
  startSecs = (startDate.getHours() * 60 * 60) + (startDate.getMinutes() * 60) + startDate.getSeconds()

  if (timerRunning)
    clearTimeout(timerID)

  check_session()
{rdelim}

function unloader() {ldelim}
{rdelim}

function check_session()
{ldelim}
  var now = new Date()
  var nowSecs = (now.getHours() * 60 * 60) + (now.getMinutes() * 60) + now.getSeconds()
  var elapsedSecs = nowSecs - startSecs;

{if $smarty.session.prefs.session_timeout eq "t"}
  if (elapsedSecs == {php}print(ini_get("session.gc_maxlifetime") - 300);{/php})
    alert('Your {$smarty.const._TITLE_} session will expire in 5 minutes!');

  if (elapsedSecs >= {php}print(ini_get("session.gc_maxlifetime"));{/php} && !sessionExpired) {ldelim}
    sessionExpired = true;
    alert('Your {$smarty.const._TITLE_} session has expired!');
  {rdelim}
{/if}

  timerID = setTimeout("check_session()",1000)
  timerRunning = true
{rdelim}

{if $smarty.session.prefs.local_tz eq "t"}
/* Retrieve user's timezone */
var d = new Date()
if (d.getTimezoneOffset) {ldelim}
  var iMinutes = d.getTimezoneOffset()
  document.cookie = "tz=" + (iMinutes / 60)
{rdelim}
{/if}
</script>
<!-- End javascript.tpl -->
{/if}

