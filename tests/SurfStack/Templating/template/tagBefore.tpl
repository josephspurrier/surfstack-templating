{* This is a single line comment *}

{*
This is a multi line comment
*}

{if (is_array($items))}
  {foreach ($items as $item)}
    * {= $item}
  {endforeach}
{elseif (is_string($items))}
  Items is a string, should be an array.
{else}
  No item has been found.
{endif}

{for ($i = 0; $i < 10; $i++)}
  * {= $i}
{endfor}

{$i = 0}
{while ($i <= 10)}
  {= $i}
  {$i++}
{endwhile}

{$i = 0}

{$i++}

{=e $i}

{switch ($i)
  case 0}
    {= "i equals 0"}
    {break}
  {case 1}
    {= "i equals 1"}
    {break}
  {case 2}
    {= "i equals 2"}
    {break}
{endswitch}

{declare(ticks=1)}
{enddeclare}