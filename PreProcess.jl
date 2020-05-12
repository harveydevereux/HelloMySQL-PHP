using CSV
using DelimitedFiles
using ProgressMeter
# remove some stop words and Punctuation
# plus make the csv nice for MySQL to read in
# without trouble
StopWords = readdlm("stopwords.txt",'\n')[:,1]
Punctuation = [",",".","\'","_","\"","?","!","|","\n"]
for csv in ["GBvideos.csv"]
    X = CSV.read("$csv")
    n = String.(names(X))
    X = Array(X)
    # get rid of the missing values
    replace!(X,missing=>"")
    rows = ["" in X[i,:] for i in 1:size(X,1)]
    Y = X[rows.==false,:]
    # now Y has none of these data rows
    @assert ("" in Y) == false
    Y = cat(zeros(1,16),Y,dims=1)
    Y[1,:] = n
    # now lets remove the stop words and Punctuation
    @showprogress for i in 2:size(Y,1)
        for j in 1:size(Y,2)
            y = Y[i,j]
            if typeof(y) == String
                y = lowercase(y)
                for p in Punctuation
                    y = replace(y,p=>"")
                end
                y = String.(split(y," "))
                to_keep = ones(size(y,1))
                for k in 1:size(y,1)
                    for s in StopWords
                        if y[k] == s
                            to_keep[k] = 0
                        end
                    end
                end
                y = y[to_keep.==1]
                Y[i,j] = join(y," ")
            end
        end
    end
    # now for each video add a channel id
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
