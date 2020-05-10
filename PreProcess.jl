using CSV
using DelimitedFiles
using ProgressMeter
for csv in ["GBvideos.csv","USvideos.csv"]
    X = CSV.read("$csv")
    n = String.(names(X))
    X = Array(X)
    replace!(X,"|"=>" ")
    replace!(X,missing=>"")
    rows = ["" in X[i,:] for i in 1:size(X,1)]
    Y = X[rows.==false,:]
    @assert ("" in Y) == false
    Y = cat(zeros(1,16),Y,dims=1)
    Y[1,:] = n
    @showprogress for i in 2:size(Y,1)
        for j in 1:size(Y,2)
            y = Y[i,j]
            if typeof(y) == String
                y = replace(y,"|"=>" ")
                y = replace(y,"\n"=>"[NEWLINE]")
                y = replace(y,","=>"[COMMA]")
                Y[i,j] = y
            end
        end
    end
    c = zeros(size(Y,1))
    AllChannels = unique(Y[2:end,4])
    println("Forming channel_id column...")
    @showprogress for i in 2:size(c,1)
        c[i] = Float64(findall(x->x==Y[i,4],AllChannels)[1])
    end
    c = Array{Any}(c)
    c[1] = "channel_id"
    c[2:end] = Int.(c[2:end])
    Y = cat(c,Y,dims=2)
    writedlm("$csv",Y,',')
end

# X = CSV.read("/home/harvey/Downloads/youtube/GBvideos.csv")
# n = String.(names(X))
# X = Array(X)
# replace!(X,missing=>"")
# rows = ["" in X[i,:] for i in 1:size(X,1)]
# Y = X[rows.==false,:]
# @assert ("" in Y) == false
# Y = cat(zeros(1,16),Y,dims=1)
# Y[1,:] = n
# @showprogress for i in 2:size(Y,1)
#     for j in 1:size(Y,2)
#         y = Y[i,j]
#         if typeof(y) == String
#             y = replace(y,"|"=>" ")
#             y = replace(y,"\n"=>"[NEWLINE]")
#             y = replace(y,","=>"[COMMA]")
#             Y[i,j] = y
#         end
#     end
# end
# Y
# c = zeros(size(Y,1))
# AllChannels = unique(Y[2:end,4])
# @showprogress for i in 2:size(c,1)
#     c[i] = Float64(findall(x->x==Y[i,4],AllChannels)[1])
# end
# c = Array{Any}(c)
# c[1] = "channel_id"
# Y = cat(c,Y,dims=2)
# writedlm("$csv",Y,',')
#
# occursin(",",y[2,16])
# String(Y[2,5])
