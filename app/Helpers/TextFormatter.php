<?php

namespace App\Helpers;

class TextFormatter
{
    /**
     * Convert text with list patterns into HTML lists
     * 
     * @param string|null $text The input text to parse
     * @param string $section The section being formatted ('pembahasan' or 'tindak_lanjut')
     * @return string The formatted HTML
     */
    public static function parseListsToHtml(?string $text, string $section = 'tindak_lanjut'): string
    {
        if (empty($text)) {
            return '';
        }

        // Split text into paragraphs
        $paragraphs = preg_split('/\n\s*\n/', $text);
        $result = [];

        foreach ($paragraphs as $paragraph) {
            $lines = explode("\n", trim($paragraph));
            
            // Check if this paragraph contains list items
            $firstLine = trim($lines[0]);
            
            // For pembahasan section, check for special formatting
            if ($section === 'pembahasan') {
                // Check if the paragraph starts with a number followed by dot and space
                if (preg_match('/^[0-9]+\.\s/', $firstLine)) {
                    // This is a main point in pembahasan
                    $mainPoint = preg_replace('/^([0-9]+\.\s)/', '<strong>$1</strong>', $firstLine);
                    $result[] = "<p class=\"pembahasan-point\">{$mainPoint}</p>";
                    
                    // Process remaining lines for sub-points
                    $subPoints = array_slice($lines, 1);
                    if (!empty($subPoints)) {
                        $subList = self::processSubPoints($subPoints);
                        if (!empty($subList)) {
                            $result[] = $subList;
                        }
                    }
                    continue;
                }
            }
            
            // Regular list detection
            if (preg_match('/^[a-z]\./', $firstLine)) {
                // Alphabetical list
                $result[] = self::createList($lines, 'ol', 'a');
            } elseif (preg_match('/^[0-9]+\./', $firstLine)) {
                // Numerical list
                $result[] = self::createList($lines, 'ol', '1');
            } elseif (preg_match('/^[\-\•]/', $firstLine)) {
                // Bullet list
                $result[] = self::createList($lines, 'ul');
            } else {
                // Regular paragraph
                $result[] = "<p>" . implode("<br>", array_map('trim', $lines)) . "</p>";
            }
        }

        return implode("\n", $result);
    }

    /**
     * Process sub-points in pembahasan section
     * 
     * @param array $lines Array of text lines
     * @return string The formatted HTML
     */
    private static function processSubPoints(array $lines): string
    {
        $items = [];
        $currentItem = '';
        $hasSubPoints = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Check for different types of sub-points
            if (preg_match('/^[a-z]\./', $line)) {
                if (!empty($currentItem)) {
                    $items[] = $currentItem;
                }
                $currentItem = $line;
                $hasSubPoints = true;
            } elseif (preg_match('/^[\-\•]/', $line)) {
                if (!empty($currentItem)) {
                    $items[] = $currentItem;
                }
                $currentItem = $line;
                $hasSubPoints = true;
            } else {
                // Continuation of previous item or standalone line
                if (empty($currentItem)) {
                    $currentItem = $line;
                } else {
                    $currentItem .= " " . $line;
                }
            }
        }

        // Add the last item
        if (!empty($currentItem)) {
            $items[] = $currentItem;
        }

        if (!$hasSubPoints) {
            // If no sub-points were found, return as indented paragraph
            return "<div class=\"pembahasan-detail\">" . 
                   implode("<br>", array_map('trim', $items)) . 
                   "</div>";
        }

        // Create a list if sub-points were found
        $listType = preg_match('/^[a-z]\./', $items[0]) ? 'ol' : 'ul';
        $style = $listType === 'ol' ? ' style="list-style-type: lower-alpha;"' : '';
        
        $html = "<div class=\"pembahasan-subpoints\">\n";
        $html .= "<{$listType}{$style}>\n";
        foreach ($items as $item) {
            $item = preg_replace('/^[a-z]\.|^[\-\•]/', '', $item);
            $html .= "  <li>" . trim($item) . "</li>\n";
        }
        $html .= "</{$listType}></div>";

        return $html;
    }

    /**
     * Create an HTML list from lines of text
     * 
     * @param array $lines Array of text lines
     * @param string $type List type ('ul' or 'ol')
     * @param string|null $style List style for ordered lists ('1', 'a', etc.)
     * @return string The formatted HTML list
     */
    private static function createList(array $lines, string $type, ?string $style = null): string
    {
        $items = [];
        $currentItem = '';
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Check if this is a new list item
            if (
                preg_match('/^[a-z]\./', $line) || 
                preg_match('/^[0-9]+\./', $line) || 
                preg_match('/^[\-\•]/', $line)
            ) {
                // Save previous item if exists
                if (!empty($currentItem)) {
                    $items[] = $currentItem;
                }
                // Start new item (remove the list marker)
                $currentItem = preg_replace('/^[a-z]\.|^[0-9]+\.|^[\-\•]/', '', $line);
            } else {
                // Continuation of previous item
                $currentItem .= " " . $line;
            }
        }
        
        // Add the last item
        if (!empty($currentItem)) {
            $items[] = $currentItem;
        }

        // Create the HTML list
        $listStyle = $style ? " style=\"list-style-type: " . ($style === 'a' ? 'lower-alpha' : 'decimal') . ";\"" : "";
        $html = "<{$type}{$listStyle}>\n";
        foreach ($items as $item) {
            $html .= "  <li>" . trim($item) . "</li>\n";
        }
        $html .= "</{$type}>";

        return $html;
    }

    /**
     * Convert HTML content to plain text format for editing
     * 
     * @param string|null $html The HTML content to convert
     * @return string The plain text format
     */
    public static function htmlToPlainText(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Remove all HTML tags except line breaks
        $text = strip_tags($html, '<br><p><ul><ol><li>');
        
        // Convert <br> tags to newlines
        $text = str_replace(['<br>', '<br/>', '<br />'], "\n", $text);
        
        // Process the HTML content
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new \DOMXPath($dom);

        $result = [];
        $currentMainPoint = '';

        // Process each element
        foreach ($xpath->query('//p|//ul|//ol') as $element) {
            if ($element->nodeName === 'p') {
                $content = trim($element->textContent);
                if (empty($content)) continue;

                // Check if it's a main point (number followed by dot)
                if (preg_match('/^[0-9]+\.\s/', $content)) {
                    if (!empty($currentMainPoint)) {
                        $result[] = $currentMainPoint;
                        $currentMainPoint = '';
                    }
                    $result[] = "\n" . $content;
                } else {
                    $result[] = $content;
                }
            } elseif ($element->nodeName === 'ul' || $element->nodeName === 'ol') {
                $items = [];
                $prefix = $element->nodeName === 'ol' ? 
                    (strpos($element->getAttribute('style'), 'lower-alpha') !== false ? 'a.' : '1.') : 
                    '-';
                
                foreach ($xpath->query('.//li', $element) as $i => $li) {
                    $content = trim($li->textContent);
                    if (empty($content)) continue;

                    if ($prefix === 'a.') {
                        $marker = chr(97 + $i) . '.';
                    } elseif ($prefix === '1.') {
                        $marker = ($i + 1) . '.';
                    } else {
                        $marker = '-';
                    }
                    
                    $items[] = $marker . ' ' . $content;
                }
                
                if (!empty($items)) {
                    $result[] = "\n" . implode("\n", $items);
                }
            }
        }

        // Clean up the result
        $text = implode("\n", $result);
        
        // Normalize line endings and remove extra whitespace
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        $text = trim($text);

        return $text;
    }

    public static function hariIndonesia($date)
    {
        $hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];

        return $hari[$date->format('l')] ?? $date->format('l');
    }
} 