# make sure you adjust this to point to the folder you want to monitor
$PathToMonitor = 'C:\Users\Public\Music\LANtrax'

#explorer $PathToMonitor

$FileSystemWatcher = New-Object System.IO.FileSystemWatcher
$FileSystemWatcher.Path  = $PathToMonitor
$FileSystemWatcher.IncludeSubdirectories = $true

# make sure the watcher emits events
$FileSystemWatcher.EnableRaisingEvents = $true

#$wshell  = New-Object -ComObject wscript.shell
# define the code that should execute when a file change is detected
$Action = {
    $details = $event.SourceEventArgs
    $Name = $details.Name
    $FullPath = $details.FullPath
    $OldFullPath = $details.OldFullPath
    $OldName = $details.OldName
    $ChangeType = $details.ChangeType
    $Timestamp = $event.TimeGenerated
    $text = "{0} was {1} at {2}" -f $FullPath, $ChangeType, $Timestamp
    Write-Host ""
    Write-Host $text -ForegroundColor Green

    $procid = Get-Process "mixxx*" | Select-Object -expand id
    $wshell  = New-Object -ComObject wscript.shell
    

    # you can also execute code based on change type here
    switch ($ChangeType)
    {
        #'Changed' { "CHANGE" }
        'Created' { "CREATED"
            # uncomment the below to mimick a time intensive handler
            Write-Host "Creation Handler Start" -ForegroundColor Gray
            Start-Sleep -Seconds 10    
            $wshell.AppActivate($procid)
            $wshell.SendKeys('%lr')
            $filename = $event.SourceEventArgs.Name
            Write-Host `Triggering Mixxx library scan due to file $filename`
            Write-Host "Creation Handler End" -ForegroundColor Gray
        }
        'Deleted' { "DELETED"
            # uncomment the below to mimick a time intensive handler
            Write-Host "Deletion Handler Start" -ForegroundColor Gray
            Start-Sleep -Seconds 10    
            Write-Host "Deletion Handler End" -ForegroundColor Gray
            $wshell.AppActivate($procid)
            $wshell.SendKeys('%lr')
            $filename = $event.SourceEventArgs.Name
            Write-Host `Triggering Mixxx library scan due to file $filename`
            Write-Host "Creation Handler End" -ForegroundColor Gray
        }
        <#
        'Renamed' { 
            # this executes only when a file was renamed
            $text = "File {0} was renamed to {1}" -f $OldName, $Name
            Write-Host $text -ForegroundColor Yellow
        }
        #>
        default { Write-Host $_ -ForegroundColor Red -BackgroundColor White }
    }
}

# add event handlers
$handlers = . {
    #Register-ObjectEvent -InputObject $FileSystemWatcher -EventName Changed -Action $Action -SourceIdentifier FSChange
    Register-ObjectEvent -InputObject $FileSystemWatcher -EventName Created -Action $Action -SourceIdentifier FSCreate
    Register-ObjectEvent -InputObject $FileSystemWatcher -EventName Deleted -Action $Action -SourceIdentifier FSDelete
    #Register-ObjectEvent -InputObject $FileSystemWatcher -EventName Renamed -Action $Action -SourceIdentifier FSRename
}

Write-Host "Watching for changes to $PathToMonitor"

try
{
    do
    {
        Wait-Event -Timeout 10
        Write-Host "." -NoNewline
        
    } while ($true)
}
finally
{
    # this gets executed when user presses CTRL+C
    # remove the event handlers
    Unregister-Event -SourceIdentifier FSChange
    Unregister-Event -SourceIdentifier FSCreate
    Unregister-Event -SourceIdentifier FSDelete
    Unregister-Event -SourceIdentifier FSRename
    # remove background jobs
    $handlers | Remove-Job
    # remove filesystemwatcher
    $FileSystemWatcher.EnableRaisingEvents = $false
    $FileSystemWatcher.Dispose()
    "Event Handler disabled."
}