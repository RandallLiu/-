<div class="col-md-6 col-lg-4">
    <div href="javascript:;" class="card">
        <div class="card-body">
            <h2 class="card-title">志愿模拟填报</h2>
            <div class="col-md-12 col-xl-12">
                <div class="mb-12">
                    <label class="form-label">所在省分</label>
                    <select type="text" class="form-select" id="select-users" value="">
                        <option value="1">北京</option>
                        <option value="2">上海</option>
                        <option value="3">广东</option>
                        <option value="4">黑龙江</option>
                    </select>
                </div>
                <div class="mb-12 mt-3">
                    <label class="form-label">选科</label>
                    <div class="form-selectgroup">
                        <label class="form-selectgroup-item">
                            <input type="checkbox" name="name" value="HTML" class="form-selectgroup-input" checked="">
                            <span class="form-selectgroup-label">物理</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="checkbox" name="name" value="CSS" class="form-selectgroup-input">
                            <span class="form-selectgroup-label">化学</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="checkbox" name="name" value="PHP" class="form-selectgroup-input">
                            <span class="form-selectgroup-label">生物</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="checkbox" name="name" value="JavaScript" class="form-selectgroup-input">
                            <span class="form-selectgroup-label">政治</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="checkbox" name="name" value="JavaScript" class="form-selectgroup-input">
                            <span class="form-selectgroup-label">历史</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="checkbox" name="name" value="JavaScript" class="form-selectgroup-input">
                            <span class="form-selectgroup-label">地理</span>
                        </label>
                    </div>
                </div>
                <div class="mb-12 mt-3">
                    <label class="form-label">分数</label>
                    <input type="text" class="form-control" placeholder="请输入预估分数">
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function (){
        window.TomSelect && (new TomSelect(document.getElementById('select-users')));
    })
</script>