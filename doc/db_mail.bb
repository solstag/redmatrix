[table]
[tr][th]Field[/th][th]Description[/th][th]Type[/th][th]Null[/th][th]Key[/th][th]Default[/th][th]Extra
[/th][/tr]
[tr][td]id[/td][td][/td][td]int(10) unsigned[/td][td]NO[/td][td]PRI[/td][td]NULL[/td][td]auto_increment
[/td][/tr]
[tr][td]convid[/td][td][/td][td]int(10) unsigned[/td][td]NO[/td][td]MUL[/td][td]0[/td][td]
[/td][/tr]
[tr][td]mail_flags[/td][td][/td][td]int(10) unsigned[/td][td]NO[/td][td]MUL[/td][td]0[/td][td]
[/td][/tr]
[tr][td]from_xchan[/td][td][/td][td]char(255)[/td][td]NO[/td][td]MUL[/td][td][/td][td]
[/td][/tr]
[tr][td]to_xchan[/td][td][/td][td]char(255)[/td][td]NO[/td][td]MUL[/td][td][/td][td]
[/td][/tr]
[tr][td]account_id[/td][td][/td][td]int(10) unsigned[/td][td]NO[/td][td]MUL[/td][td]0[/td][td]
[/td][/tr]
[tr][td]channel_id[/td][td][/td][td]int(10) unsigned[/td][td]NO[/td][td]MUL[/td][td]NULL[/td][td]
[/td][/tr]
[tr][td]title[/td][td][/td][td]text[/td][td]NO[/td][td][/td][td]NULL[/td][td]
[/td][/tr]
[tr][td]body[/td][td][/td][td]mediumtext[/td][td]NO[/td][td][/td][td]NULL[/td][td]
[/td][/tr]
[tr][td]attach[/td][td][/td][td]mediumtext[/td][td]NO[/td][td][/td][td]NULL[/td][td]
[/td][/tr]
[tr][td]mid[/td][td][/td][td]char(255)[/td][td]NO[/td][td]MUL[/td][td]NULL[/td][td]
[/td][/tr]
[tr][td]parent_mid[/td][td][/td][td]char(255)[/td][td]NO[/td][td]MUL[/td][td]NULL[/td][td]
[/td][/tr]
[tr][td]created[/td][td][/td][td]datetime[/td][td]NO[/td][td]MUL[/td][td]0000-00-00 00:00:00[/td][td]
[/td][/tr]
[tr][td]expires[/td][td][/td][td]datetime[/td][td]NO[/td][td]MUL[/td][td]0000-00-00 00:00:00[/td][td]
[/td][/tr]
[/table]

Return to [zrl=[baseurl]/help/database]database documentation[/zrl]