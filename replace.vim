:%s/if (\$sOperador === 'BETWEEN'/if ($sOperador == 'BETWEEN'/
:%s/\(if (\$sOperador == 'BETWEEN'.*\)) unset/\1 || $sOperador == 'OR') unset/
:wq
