<?php
class Node {
    public $value;
    public $children;

    public function __construct($value) {
        $this->value = $value;
        $this->children = array();
    }

    public function addChild($node) {
        $this->children[] = $node;
        return $node;
    }

    public function removeChild($node) {
        $key = array_search($node, $this->children, true);
        if ($key !== false) {
            unset($this->children[$key]);
            $this->children = array_values($this->children);
        }
    }

    public function traverse($callback) {
        $callback($this);
        foreach ($this->children as $child) {
            $child->traverse($callback);
        }
    }

    public function find($value) {
        if ($this->value === $value) {
            return $this;
        }
        foreach ($this->children as $child) {
            $result = $child->find($value);
            if ($result !== null) {
                return $result;
            }
        }
        return null;
    }
}

// Usage example:
$root = new Node("Root");
$child1 = $root->addChild(new Node("Child 1"));
$child2 = $root->addChild(new Node("Child 2"));
$child1->addChild(new Node("Grandchild 1"));

// Traverse the tree
$root->traverse(function($node) {
    echo $node->value . "\n";
});
