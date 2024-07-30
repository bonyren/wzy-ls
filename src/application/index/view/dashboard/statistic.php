<div class="dashboard d-flex flex-row justify-content-around align-items-center" style="height: 100%;overflow: auto">

        <div class="info-box red-bg d-flex justify-content-center align-items-center">
            <div><i class="fa fa-users fa-3x"></i></div>
            <div>
                <div class="count"><?=$bindValues['statistic']['totalSubscriberCount']?></div>
                <div class="title">总订阅者</div>
                <div class="sub-title">有效订阅者</div>
                <div class="count"><?=$bindValues['statistic']['totalSubscriberAvailableCount']?></div>
            </div>
        </div>

        <div class="info-box orange-bg d-flex justify-content-center align-items-center">
            <div><i class="fa fa-cubes fa-3x"></i></div>
            <div>
                <div class="count"><?=$bindValues['statistic']['totalCampaignCount']?></div>
                <div class="title">总投递活动</div>
                <div class="sub-title">"投递中"活动</div>
                <div class="count"><?=$bindValues['statistic']['totalCampaignRunningCount']?></div>
            </div>
        </div>

        <div class="info-box green-bg d-flex justify-content-center align-items-center">
            <div>
                <i class="fa fa-paper-plane-o fa-3x"></i>
            </div>
            <div>
                <div class="count"><?=$bindValues['statistic']['totalSentCount']?></div>
                <div class="title">总投递</div>
                <div class="sub-title">近一个月投递</div>
                <div class="count"><?=$bindValues['statistic']['totalSentRecentCount']?></div>
            </div>
        </div>

        <div class="info-box magenta-bg d-flex justify-content-center align-items-center">
            <div class="float-left">
                <i class="fa fa-navicon fa-3x"></i>
            </div>
            <div class="float-left">
                <div class="count"><?=$bindValues['statistic']['totalEventCount']?></div>
                <div class="title">事件总数</div>
            </div>
        </div>

</div>