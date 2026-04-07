def needleman_wunsch_similarity(a, b):
    a = a.lower().strip()
    b = b.lower().strip()

    match = 1
    mismatch = -1
    gap = -1

    m, n = len(a), len(b)
    dp = [[0] * (n + 1) for _ in range(m + 1)]

    for i in range(m + 1):
        dp[i][0] = i * gap
    for j in range(n + 1):
        dp[0][j] = j * gap

    for i in range(1, m + 1):
        for j in range(1, n + 1):
            if a[i - 1] == b[j - 1]:
                score = match
            else:
                score = mismatch
            dp[i][j] = max(
                dp[i - 1][j - 1] + score,
                dp[i - 1][j] + gap,
                dp[i][j - 1] + gap
            )

    max_score = dp[m][n]
    max_possible = max(m, n) * match
    return max_score / max_possible if max_possible else 0
