using DelimitedFiles
using Plots
using LinearAlgebra
using Statistics
using Dates
using ProgressMeter

D = readdlm("/home/harvey/HelloMySQL/GBvideos.csv",',')

cols = D[1,:]
D = D[2:end,:]

index_map = (n) -> findall(x->x==n,cols)[1]

TrendingDate = D[:,index_map("trending_date")]
[TrendingDate[i]="20"*TrendingDate[i] for i in 1:size(TrendingDate,1)]
VideoIds = unique(D[:,index_map("video_id")])
VideoInds = []
@showprogress for id in VideoIds
    x = findall(x->x==id,D)
    push!(VideoInds,[x[i][1] for i in 1:size(x,1)])
end
VideoInds
TrendingLength = function(id,D)
    ind = VideoInds[findall(x->x==id,VideoIds)][1]
    d=Date.(TrendingDate[ind,:],"yy.dd.mm")
    return d[end]-d[1]
end

# 3 mins
@time TrendingLengths = [TrendingLength(VideoIds[i],D) for i in 1:size(VideoIds,1)]
histogram(TrendingLengths,bins=50)

Likes = function(id,D)
    ind = VideoInds[findall(x->x==id,VideoIds)][1]
    return  D[ind,index_map("likes")]
end

Dislikes = function(id,D)
    ind = VideoInds[findall(x->x==id,VideoIds)][1]
    return D[ind,index_map("dislikes")]
end

LikeDislikeRatio = function(id,D)
    ind = VideoInds[findall(x->x==id,VideoIds)][1]
    return Likes[ind,:]./DisLikes[ind,:]
end

Comments = function(id,D)
    ind = VideoInds[findall(x->x==id,VideoIds)][1]
    if size(ind,1) > 0
        d = D[ind,index_map("comment_count")]
        return d
    else
        return [0.0]
    end
end

Views = function(id,D)
    ind = VideoInds[findall(x->x==id,VideoIds)][1]
    if size(ind,1) > 0
        d = D[ind,index_map("views")]
        return d
    else
        return [0.0]
    end
end

channel = function(id,D)
    ind = VideoInds[findall(x->x==id,VideoIds)][1]
    d = D[ind[1],index_map("channel_title")]
    return d
end

#Channels = unique([channel(VideoIds[i],D) for i in 1:size(VideoIds,1)])
# 12 mins
@time L = [Likes(VideoIds[i],D) for i in 1:size(VideoIds,1)]
@time Dis = [Dislikes(VideoIds[i],D) for i in 1:size(VideoIds,1)]
@time LDRatios = [L[i]./Dis[i] for i in 1:size(VideoIds,1)]
@time C = [Comments(VideoIds[i],D) for i in 1:size(VideoIds,1)]
meanLDRatios = [mean(LDRatios[i]) for i in 1:size(LDRatios,1)]
InitialLikes = [L[i][1] for i in 1:size(LDRatios,1)]
InitialDisLikes = [Dis[i][1] for i in 1:size(LDRatios,1)]
InitialComments = [C[i][1] for i in 1:size(C,1)]
@time V = [Views(VideoIds[i],D)[1] for i in 1:size(VideoIds,1)]
scatter(meanLDRatios,TrendingLengths)
scatter(InitialLikes,TrendingLengths)
scatter(1.0./InitialDisLikes,TrendingLengths)
scatter(InitialComments,TrendingLengths)
scatter(V,TrendingLengths)
Channels = [channel(VideoIds[i],D) for i in 1:size(VideoIds,1)]
AllChannels = unique([channel(VideoIds[i],D) for i in 1:size(VideoIds,1)])
scatter(Channels,TrendingLengths)

TrendingLengths

Channels[sortperm(TrendingLengths)]

T = Day.(zeros(length(AllChannels)))
for i in 1:size(AllChannels,1)
    ind = findall(x->x==AllChannels[i],Channels)
    T[i] = sum(TrendingLengths[ind])
end
scatter(AllChannels,T)
