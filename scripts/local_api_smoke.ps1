<#!
VOSIZ Local API Smoke Test (PowerShell)
Usage:
  1. Adjust $BaseUrl if needed (artisan serve vs Apache path).
  2. (Optional) Set $UserToken and $AdminToken with generated Sanctum PATs.
  3. Run: powershell -ExecutionPolicy Bypass -File .\scripts\local_api_smoke.ps1
#>

# -------- CONFIG --------
$BaseUrl    = $env:VOSIZ_BASE_URL
if (-not $BaseUrl -or $BaseUrl -eq '') { $BaseUrl = 'http://127.0.0.1:8001' }  # change to http://localhost/vosiz-mens-wellness-ecommerce/public if using Apache
$UserToken  = $env:VOSIZ_USER_TOKEN   # optional regular user token
$AdminToken = $env:VOSIZ_ADMIN_TOKEN  # optional admin token

Write-Host "== VOSIZ Local API Smoke Test ==" -ForegroundColor Cyan
Write-Host "Base URL: $BaseUrl"

function Test-Endpoint {
    param(
        [string]$Name,
        [string]$Method = 'GET',
        [string]$Path,
        [string]$Body = '',
        [string]$Token = ''
    )
    $Url = "$BaseUrl$Path"
    $Headers = @{ 'Accept' = 'application/json' }
    if ($Token) { $Headers['Authorization'] = "Bearer $Token" }

    try {
        if ($Method -eq 'GET') { $resp = Invoke-WebRequest -Uri $Url -Headers $Headers -Method GET -ErrorAction Stop }
        elseif ($Method -eq 'POST') { $resp = Invoke-WebRequest -Uri $Url -Headers ($Headers + @{ 'Content-Type'='application/json'}) -Method POST -Body $Body -ErrorAction Stop }
        elseif ($Method -eq 'DELETE') { $resp = Invoke-WebRequest -Uri $Url -Headers $Headers -Method DELETE -ErrorAction Stop }
        else { throw "Unsupported method $Method" }
        $code = $resp.StatusCode
        $ok   = $code -ge 200 -and $code -lt 300
        $status = if ($ok) { 'PASS' } else { 'FAIL' }
        Write-Host ("[{0}] {1} ({2}) -> {3}" -f $status,$Name,$code,$Url) -ForegroundColor (if ($ok) { 'Green' } else { 'Red' })
    }
    catch {
        Write-Host ("[ERR ] $Name -> $Url :: $($_.Exception.Message)") -ForegroundColor Red
    }
}

# Public endpoints
Test-Endpoint -Name "Featured Products" -Path "/api/products/featured"
Test-Endpoint -Name "Active Categories" -Path "/api/categories"
Test-Endpoint -Name "Search Products" -Path "/api/products/search?q=test"

# Authenticated (if user token present)
if ($UserToken) {
    Test-Endpoint -Name "Current User" -Path "/api/user" -Token $UserToken
    Test-Endpoint -Name "User Orders" -Path "/api/orders" -Token $UserToken
}
else {
    Write-Host "[SKIP] Auth checks (set VOSIZ_USER_TOKEN env var)" -ForegroundColor Yellow
}

# Admin endpoints
if ($AdminToken) {
    Test-Endpoint -Name "Admin Products" -Path "/api/admin/products" -Token $AdminToken
    # Create product (silent validation if category 1 missing)
    $newProd = '{"name":"Smoke Product","description":"Created in smoke test","price":9.99,"category_id":1}'
    Test-Endpoint -Name "Admin Create Product" -Path "/api/admin/products" -Method POST -Token $AdminToken -Body $newProd
}
else {
    Write-Host "[SKIP] Admin checks (set VOSIZ_ADMIN_TOKEN env var)" -ForegroundColor Yellow
}

Write-Host "== Done ==" -ForegroundColor Cyan
