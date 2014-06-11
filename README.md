[Envi PHP](http://www.enviphp.net/) - Light Weight PHP FlameWork
==================================================

Contribution Guides
--------------------------------------

オープンソースである、EnviMVCに貢献くださる方は、ガイドを読んでいただけると幸いです。

コードの修正や、バグの報告に関しては下記のリンクを参照してください。

1. [ソースコードのフォーク](https://github.com/EnviMVC/EnviMVC3PHP)
2. [マニュアル・wiki](https://github.com/EnviMVC/EnviMVC3PHP/wiki)
3. [バグ報告](https://github.com/EnviMVC/EnviMVC3PHP/issues)



コードの実行環境
--------------------------------------

PHP5.2～PHP5.4が正常動作する環境での開発を行って下さい。
PHP5.2～PHP5.4で互換を保つようにして下さい。
v3.3.4 から、PHP5.4も正式対応となりました。


PullRequestについて
--------------------------------------

コードの修正をいただいたら、EnviMVC宛てに、PullRequestを送って下さい。
修正のコミットは、一つにまとめていただけると幸いです。
下記にその方法を示します。


```
git checkout -b pullpeq/任意のブランチの名前
git merge --squash 今まで変更していたブランチ名
git commit

```

また、PullRequestでのコメントは、何を変更したのか。何のための変更なのか。を記載してください。
それ以外のルールはありません。



コーディング規約について
--------------------------------------

Pear標準コーディング規約に準拠する必要があります。
規約については[こちら](http://pear.php.net/manual/ja/standards.php)から参照することが出来ます。



バグ報告について
--------------------------------------

コードのコミットがなくとも、バグの報告、機能改善の要求だけでも、十分なプロジェクトへの貢献となりますので、発見し次第、[こちら](https://github.com/EnviMVC/EnviMVC3PHP/issues)から報告して下さい。


Contribution Guides
--------------------------------------

Is an open source , who hope you'll give me contribute to EnviMVC, I am happy when you can read the guide .

Please refer to the link below and modification of the code , with respect to report bugs .


1. [ Fork of the source code ](Https://github.com/EnviMVC/EnviMVC3PHP)
2. [Manual · wiki] (https://github.com/EnviMVC/EnviMVC3PHP/wiki)
3. [Bug report ] (https://github.com/EnviMVC/EnviMVC3PHP/issues)



Execution environment of code
--------------------------------------

Should be made to develop in an environment that PHP5.2 .. PHP5.4 to normal operation.
Try to keep compatibility with PHP5.2 .. PHP5.4. 
from v3.3.4, PHP5.4 was also a corresponding formally.


About PullRequest
--------------------------------------

If you received a modification of the code , in EnviMVC addressed , please send PullRequest.
Commit fix , I hope you can be summarized in one .
I shows you how below .


~~~
git checkout -b pullpeq/<Name of the branch of any>
git merge --squash <Branch name you have changed until now>
git commit

~~~

Also , what comments in PullRequest is , did they change . Is it a change for anything . Please include .
There is no other rules .



For coding conventions
--------------------------------------

You must conform to Pear Coding Standards .
[Here will be able to see from] (http://pear.php.net/manual/ja/standards.php) for terms .



For bug report
--------------------------------------

Even if there is no commit code , bug reports , alone request of functional improvement , it will be contributing to the project enough , as soon as discovered , [here ] (https://github.com/EnviMVC/EnviMVC3PHP/issues) Please report it .
