import { useEffect, useMemo, useState } from 'react';

type HeatmapBucket = {
  column: string;
  duration_seconds: number;
  duration_label: string;
  intensity: 'safe' | 'warning' | 'critical';
  color: string;
};

type TimelineEvent = {
  task_id: number;
  old_status: string | null;
  new_status: string;
  changed_at: string;
  changed_by?: string;
};

type KanbanAnalyticsProps = {
  heatmap: HeatmapBucket[];
  timeline: { bucket: string; events: TimelineEvent[]; counts: Record<string, number> }[];
};

const heatmapBadge = (intensity: HeatmapBucket['intensity']) => {
  switch (intensity) {
    case 'critical':
      return 'text-red-700';
    case 'warning':
      return 'text-orange-700';
    default:
      return 'text-emerald-700';
  }
};

export default function KanbanPlaybackHeatmap({ heatmap, timeline }: KanbanAnalyticsProps) {
  const [playbackIndex, setPlaybackIndex] = useState(0);
  const [isPlaying, setIsPlaying] = useState(false);

  const buckets = useMemo(() => timeline.map(slot => slot.bucket), [timeline]);

  useEffect(() => {
    if (!isPlaying || buckets.length === 0) {
      return;
    }

    const interval = window.setInterval(() => {
      setPlaybackIndex(current => {
        if (current + 1 >= buckets.length) {
          window.clearInterval(interval);
          setIsPlaying(false);
          return current;
        }

        return current + 1;
      });
    }, 900);

    return () => window.clearInterval(interval);
  }, [isPlaying, buckets.length]);

  const activeSlot = timeline[playbackIndex] ?? timeline[0];

  return (
    <div className="space-y-6">
      <section className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {heatmap.map(bucket => (
          <div key={bucket.column} className={`rounded-3xl border border-white/10 p-4 shadow-lg ${bucket.color}`}>
            <div className="flex items-center justify-between gap-3">
              <h3 className="font-semibold text-slate-900">{bucket.column}</h3>
              <span className={`rounded-full px-3 py-1 text-xs font-semibold ${heatmapBadge(bucket.intensity)}`}>
                {bucket.intensity.toUpperCase()}
              </span>
            </div>
            <p className="mt-3 text-sm text-slate-700">
              {bucket.duration_label} idle
            </p>
          </div>
        ))}
      </section>

      <section className="rounded-3xl border border-border bg-white/80 p-5 shadow-xl backdrop-blur">
        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p className="text-sm uppercase tracking-[0.2em] text-slate-500">Playback Matrix</p>
            <h2 className="text-2xl font-semibold text-slate-900">Task migration time-lapse</h2>
          </div>
          <button
            type="button"
            onClick={() => {
              setPlaybackIndex(0);
              setIsPlaying(true);
            }}
            className="inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700"
          >
            Play
          </button>
        </div>

        <div className="mt-5 grid gap-4 lg:grid-cols-2">
          <div className="rounded-3xl border border-border p-4 bg-slate-50">
            <p className="text-sm font-medium text-slate-600">Current bucket</p>
            <p className="mt-2 text-lg font-semibold text-slate-900">{activeSlot?.bucket || 'No activity'}</p>
            <div className="mt-4 space-y-2 text-sm text-slate-700">
              <p>{activeSlot?.events.length ?? 0} transition events</p>
              <p>{Object.entries(activeSlot?.counts ?? {}).map(([status, count]) => `${status}: ${count}`).join(' · ')}</p>
            </div>
          </div>

          <div className="space-y-3 rounded-3xl border border-border p-4 bg-slate-50">
            <h3 className="text-sm font-semibold text-slate-900">Playback controls</h3>
            <p className="text-sm text-slate-600">Use the play button to animate status migration across the selected range.</p>
            <div className="flex items-center gap-3">
              <button
                type="button"
                className="rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                onClick={() => setPlaybackIndex(index => Math.max(index - 1, 0))}
              >
                Prev
              </button>
              <button
                type="button"
                className="rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                onClick={() => setPlaybackIndex(index => Math.min(index + 1, buckets.length - 1))}
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
