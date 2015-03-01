'use strict';

var RangeStore = function() {
    this.fetchedRanges = [];
};

RangeStore.prototype.isRangeFetched = function(range) {
    
    for(var index in this.fetchedRanges) {
        
        var fetchedRange = this.fetchedRanges[index];

        if(
            range.start.isSame(fetchedRange.start) ||
            range.start.isAfter(fetchedRange.start)
        ) {
            if(
                range.end.clone().add(1, 'second').isSame(fetchedRange.end.clone().add(1, 'second')) ||
                range.end.clone().add(1, 'second').isBefore(fetchedRange.end.clone().add(1, 'second'))
            ) {
                return true;
            } else {
                return false
            }
        }
    }

    return false;
};

RangeStore.prototype.aggregateRange = function(rangeB) {

    rangeB.end = rangeB.end.clone().add(1, 'second');

    if(this.fetchedRanges.length === 0) {
        this.fetchedRanges.push(rangeB);
        return;
    }

    var sortFunction = function(a, b) { return a.start.isAfter(b.start);};

    // Sorting ranges by start date
    this.fetchedRanges.sort(sortFunction);

    var newRanges = [],
        rangeBPushed = false;

    // 1: A--A   B-----B
    // 2: A--B----B----A
    // 3: A----B--A----B

    for(var index in this.fetchedRanges) {
        var rangeA = this.fetchedRanges[index];

        // console.log('B.start:' + rangeB.start.toISOString() + '; B.end:' + rangeB.end.toISOString() + '; A.start:' + rangeA.start.toISOString() + '; A.end: ' + rangeA.end.toISOString())

        if(rangeB.start.isAfter(rangeA.end)) {
            // 1: Ranges are disjoint; we simply add the given range to the collection of fetched ranges
            newRanges.push(rangeB);
            continue;
        } else {
            // => rangeB.start is before rangeA.end or same as rangeA.end

            if(rangeB.end.isSame(rangeA.start)) {
                // 3: Ranges are consecutive; we do merge them

                newRanges.push({
                    start: rangeB.start.clone(),
                    end: rangeA.end.clone()
                });

                rangeBPushed = true;
                continue;
            }

            if(rangeB.end.isBefore(rangeA.start)) {
                // 1: Ranges are disjoint; we push them both

                newRanges.push(rangeB);
                newRanges.push(rangeA);

                rangeBPushed = true;
                continue;
            }

            if(
                rangeB.end.isBefore(rangeA.end) ||
                rangeB.end.isSame(rangeA.end)
            ) {
                if(rangeB.start.isBefore(rangeA.start)) {
                    // 3: Ranges are overlapping or consecutive; we merge them

                    newRanges.push({
                        start: rangeB.start.clone(),
                        end: rangeA.end.clone()
                    });
                } else {
                    // 2: Range B is included in range A; we just push Range A
                    newRanges.push(rangeA);
                }

                rangeBPushed = true;
                continue;
            }
            
            // 3: Ranges are overlapping; we merge them
            newRanges.push({
                start: rangeA.start.clone(),
                end: rangeB.end.clone()
            });

            rangeBPushed = true;
        }
    }

    if(!rangeBPushed) {
        // Range B has not been handled; we add it to the fetched range collection
        newRanges.push(rangeB);
    }

    // Sorting new ranges by start date
    newRanges.sort(sortFunction);

    this.fetchedRanges = newRanges;

    return null;
}

RangeStore.prototype.debugFetchranges = function() {
    var res = [];

    for(var index in this.fetchedRanges) {
        var fRange = this.fetchedRanges[index];
        res.push(fRange.start.toString() + ' -> ' + fRange.end.toString());
    }

    return res.join('\n');
};

module.exports = RangeStore;