This addon lets you build MOOCs and other types of courses with the redmatrix webpages feature.

It provies a layout template to present a sequence of learning resources where your progress is tracked and your feedback can be shared and discussed with other course participants and/or your channel's stream.

Building a course:
*In the webpages module, create a new layout using the "sequence" template
*Place [widget=coursetabs]...[/widget] and [widget=coursetabs][/widget] in your layout
*Build the menu by passing a sequence of variables to the widget: header_title_$id and item_{href,title}_$id
*Menu will be ordered like the variables passed
*Sequence tabs are webpage blocks named "$pagename-seq-$tabname"
*Tabs are ordered according to php string comparison on $tabname
*For database reasons, pagepath<47 and tag<32 must be observed, affecting channel, page and tag names
