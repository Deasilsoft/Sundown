<?php

namespace Deasilsoft;

include_once "Sundown.class.php";

$sundown = new Sundown();

echo $sundown->convert("

Big Header
============

[[YouTube]](M7lc1UVf-VE)

[[YouTube]]

[[YouTube|M7lc1UVf-VE]]

[[gds|M7lc1UVf-VE]]

[[vimeo|M7lc1UVf-VE]]

[[TWITCH|M7lc1UVf-VE]]




![Alt Text](http://imagehost.com/image.png \"Some Title, YO!\")

![Alt Text 2](http://imagehost.com/image2.png)





[Test Link](http://google.com/ \"This leads to google!\")

[Test Link](http://google.com/)

  

``` Javascript
TestScript();
**NO MARKDOWN**
# EZ
```



> Test Quote  
> Continues
New Paragraph


===


# This is a heading

This ~~is a~~ paragraph.  
  
This is the same paragraph.

This is a new paragraph.

* This
* is
* a

 SURPRISE

 THIS  
 IS  
 AWESOME
* list

This is *a __TEST__ new __\*TEST\*__* **paragraph _TEST_**.  
This is the `**same**` paragraph, but different.




**SIMPLE STRONG**


1. LOL
2. DOGGO

   1. Test

   2. Test

500. This is three.



*** Description List Title
++ Description List Item
++ Description List Item
-- Description List Item
-- Description List Item
*** Description List Title
*** Description List Title
++ Description List Item
-- Description List Item






| Test |
| One Column |
| Suddenly | Two |
| Then Back |
| To one |

| Pretty Table | With Pretty Columns |
| ============ | =================== |
|    Test     >|        Rows         |
|    Test     <|        Rows         |
|    Test     >|        Rows         |
|    Test     <|        Rows         |
|    Test     >|        Rows         |
|    Test     <|        Rows         |

                
            

This is a new paragraph.


");
