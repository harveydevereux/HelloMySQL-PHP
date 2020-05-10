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
# writedlm("$csv",Y,',')
#
# occursin(",",y[2,16])
# String(Y[2,5])
