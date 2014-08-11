Yii-Ueditor
===========

Yii-Ueditor插件是使用Ueditor 1.4.3开发的。

该插件在https://github.com/xbzbing/yii1-ueditor-ext 基础上进行开发。在最小破环源码的基础下，添加了七牛上传、图片管理。

<ul class="task-list">
<li>
1、将ueditor放在项目的/protected/extensions/目录下。
</li>
<li>
2、在config.php中配置controllerMap，来指定ueditor的访问路径

<div class="highlight highlight-php"><pre><span class="s1">'controllerMap'</span><span class="o">=&gt;</span><span class="k">array</span><span class="p">(</span>
    <span class="s1">'ueditor'</span><span class="o">=&gt;</span><span class="k">array</span><span class="p">(</span>
        <span class="s1">'class'</span><span class="o">=&gt;</span><span class="s1">'ext.ueditor.XUeditorController'</span><span class="p">,</span>
    <span class="p">),</span>
<span class="p">),</span>
<iframe id="tmp_downloadhelper_iframe" style="display: none;"></iframe></pre></div>

<pre><code>可选配置：
</code></pre>

<div class="highlight highlight-php"><pre><span class="s1">'controllerMap'</span><span class="o">=&gt;</span><span class="k">array</span><span class="p">(</span>
    <span class="s1">'ueditor'</span><span class="o">=&gt;</span><span class="k">array</span><span class="p">(</span>
        <span class="s1">'class'</span><span class="o">=&gt;</span><span class="s1">'ext.ueditor.UeditorController'</span><span class="p">,</span>
        <span class="s1">'config'</span><span class="o">=&gt;</span><span class="k">array</span><span class="p">(),</span><span class="c1">//参考config.json的配置，此处的配置具备最高优先级</span>
        <span class="s1">'useQiniu'</span><span class="o">=&gt;</span><span class="k">true</span><span class="p">,</span><span class="c1">//是否使用七牛存储</span>
        <span class="s1">'thumbnail'</span><span class="o">=&gt;</span><span class="k">true</span>,</span><span class="c1">//是否开启缩略图</span>
        <span class="s1">'watermark'</span><span class="o">=&gt;</span><span class="s1">''</span><span class="p">,</span><span class="c1">//水印图片的地址，使用相对路径</span>
        <span class="s1">'locate'</span><span class="o">=&gt;</span><span class="mi">9</span><span class="p">,</span><span class="c1">//水印位置，1-9，默认为9在右下角</span>
    <span class="p">),</span>
<span class="p">),</span>
</pre></div>

<p></p>
</li>
<li>
3、在view中使用widget。<br>
    在原有的view中添加即可，注意id填写为原有的textarea的id。<br>
    注意，使用这个widget时，不要删除原有的代码，只要添加此处的代码即可。

<div class="highlight highlight-php"><pre><span class="nv">$this</span><span class="o">-&gt;</span><span class="na">widget</span><span class="p">(</span><span class="s1">'ext.ueditor.UeditorWidget'</span><span class="p">,</span>
        <span class="k">array</span><span class="p">(</span>
                <span class="s1">'id'</span><span class="o">=&gt;</span><span class="s1">'Post_content'</span><span class="p">,</span><span class="c1">//页面中输入框（或其他初始化容器）的ID</span>
                <span class="s1">'name'</span><span class="o">=&gt;</span><span class="s1">'editor'</span><span class="p">,</span><span class="c1">//指定ueditor实例的名称,个页面有多个ueditor实例时使用</span>
        <span class="p">)</span>
<span class="p">);</span>
</pre></div>

<div class="highlight highlight-php"><pre><span class="nv">$this</span><span class="o">-&gt;</span><span class="na">widget</span><span class="p">(</span><span class="s1">'ext.ueditor.UeditorWidget'</span><span class="p">,</span>
        <span class="k">array</span><span class="p">(</span>
                <span class="s1">'model'</span><span class="o">=&gt;</span><span class="s1">$model</span><span class="p">,</span><span class="c1">//model</span>
                <span class="s1">'attribute'</span><span class="o">=&gt;</span><span class="s1">''</span><span class="p">,</span><span class="c1">//attribute</span>
        <span class="p">)</span>
<span class="p">);</span>
</pre></div>

<p></p>
</li>
<li>
4、错误排除<br>
出现错误请查看上传目录的权限问题。<br>
默认上传到「应用」根目录（不是网站根目录）的upload/目录。<br>
不要开启Yii的调试，因为UEditor的返回都是json格式，开启调试会导致返回格式不识别。
</li>

<h2>
<a name="user-content-%E5%85%B6%E4%BB%96%E8%AF%B4%E6%98%8E" class="anchor" href="#%E5%85%B6%E4%BB%96%E8%AF%B4%E6%98%8E" aria-hidden="true"><span class="octicon octicon-link"></span></a>其他说明</h2>

<p>1、原1.3.6版本插件<br>
因为1.3.6版本作为一个比较稳定的版本，还是具备一定的使用价值（支持IE6/7，1.4.3以上版本将不再承诺支持IE6/7），因此保留下载地址。<br>
下载地址：<a href="http://www.crazydb.com/upload/file/20140531/7384_yii-ext-ueditor136.tar.gz">http://www.crazydb.com/upload/file/20140531/7384_yii-ext-ueditor136.tar.gz</a><br>
参考：<a href="http://www.crazydb.com/archive/%E7%99%BE%E5%BA%A6%E7%BC%96%E8%BE%91%E5%99%A8UEditor%E7%9A%84Yii%E6%89%A9%E5%B1%95">http://www.crazydb.com/archive/百度编辑器UEditor的Yii扩展</a><br>
2、1.4.3版本插件<br>
参考地址：<a href="http://www.crazydb.com/archive/UEditor1.4.3-for-Yii1-%E6%89%A9%E5%B1%95">http://www.crazydb.com/archive/UEditor1.4.3-for-Yii1-扩展</a></p>
</ul>
