$Path = 'C:\Users\Public\Music\LANtrax'

$watcher = New-Object System.IO.FileSystemWatcher
$watcher.Path = $Path
$watcher.Filter = '*.*'
$watcher.IncludeSubdirectories = $true
$watcher.EnableRaisingEvents = $true

Write-Warning "FileSystemWatcher is monitoring $Path"

$wshell  = New-Object -ComObject wscript.shell
$domything = {
    Write-Output 'TEST'
    $wshell.AppActivate('Mixxx')
    $wshell.SendKeys({F2})
    $filename = $event.SourceEventArgs.Name
    Write-Output 'Triggering Mixxx library scan due to add of file $filename'
}

#Register-ObjectEvent $watcher -EventName 'Created' -SourceIdentifier 'MixxxAutoUpdate1c' -Action $domything
#Register-ObjectEvent $watcher -EventName 'Changed' -SourceIdentifier 'MixxxAutoUpdate2c' -Action $domything
Register-ObjectEvent -InputObject $watcher -EventName Created -Action $domything -SourceIdentifier FSCreate3

try {
    do
    {
        Wait-Event -Timeout 1
        Write-Host "." -NoNewline
    } while ($true)
} finally {
    $watcher.Dispose()
    Unregister-Event -SourceIdentifier 'MixxxAutoUpdate'
    Write-Output 'Unregistered FileSystemWatcher'
}

#while ($true) {Start-Sleep 5}