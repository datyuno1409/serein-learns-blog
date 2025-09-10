$ErrorActionPreference = 'Stop'

$s = New-Object Microsoft.PowerShell.Commands.WebRequestSession
# Prime session by loading login page
$null = Invoke-WebRequest -UseBasicParsing http://localhost:8000/login -WebSession $s -MaximumRedirection 0

# Submit login form
$login = Invoke-WebRequest -UseBasicParsing http://localhost:8000/login -Method Post -Body 'username=admin&password=admin123' -Headers @{ 'Content-Type'='application/x-www-form-urlencoded' } -WebSession $s -MaximumRedirection 0

"LoginStatus=$($login.StatusCode)"
if ($login.Headers.Location) { "LoginLocation=$($login.Headers.Location)" }

# Access dashboard with same session
$dash = Invoke-WebRequest -UseBasicParsing http://localhost:8000/admin/dashboard -WebSession $s -MaximumRedirection 0
"DashboardStatus=$($dash.StatusCode)"
if ($dash.Headers.Location) { "DashboardLocation=$($dash.Headers.Location)" }

$snippet = $dash.Content
if ($snippet.Length -gt 200) { $snippet = $snippet.Substring(0,200) }
"DashboardSnippet=$snippet"