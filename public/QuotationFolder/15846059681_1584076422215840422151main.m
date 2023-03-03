Sub={'Fai','Salam','Asia','Alex'}; Nsub=length(Sub);
Date={'2-7-2020','2-10-2020','2-12-2020','2-13-2020'};
Nblk=5;
Fs=90;
Ntar=14;

for sub=[4 1 2]%1:Nsub
    for blk=1:Nblk
        FileName=sprintf('VRtest_%s_%s_Block%d.xdf',Date{sub},Sub{sub},blk);
        Data=load_xdf(FileName);
        [tmp,Ndat]=size(Data);
        pause
        Ntr=1;
        Mov=zeros(Ndat,Ntr); % movement (all trials)
        TarInd=zeros(Ntr,1); % target location
        HandInd=zeros(Ntr,1); % hand index
        
        for i=1:Ndat
            if (strncmp('S',Data{i}.info.name,1))
                spawnData=Data{i}.time_series; % has the targetID information
                Tspawn=Data{i}.time_stamps; % has the task StartTime info
            elseif (strncmp('L',Data{i}.info.name,1))
                Xleft=Data{i}.time_series; %4, 5, 6 rows are x, y, and z
                Tleft=Data{i}.time_stamps;
            elseif (strncmp('R',Data{i}.info.name,1))
                Xright=Data{i}.time_series; %4, 5, 6 rows are x, y, and z
                Tright=Data{i}.time_stamps;
            elseif (strncmp('C',Data{i}.info.name,1))
                Xhead=Data{i}.time_series;
                Thead=Data{i}.time_stamps;
            elseif (strncmp('H',Data{i}.info.name,1))
                Xcol=Data{i}.time_series;
                Tcol=Data{i}.time_stamps;
            end
        end
        
        % Time initialization
        Ti=mean([Tright(1) Tleft(1) Thead(1)]);
        Tspawn=Tspawn'-Ti;
        Tleft=Tleft'-Ti;
        Tright=Tright'-Ti;
        Thead=Thead'-Ti;
        Tcol=Tcol'-Ti;
        % Data format (transpose)
        spawnData=spawnData';
        Xright=Xright';
        Xleft=Xleft';
        Xhead=Xhead';
        Xcol=Xcol';
        % Angle correction
        for k=1:3
            Ind=find(Xleft(:,k)>180);
            Xleft(Ind,k)=Xleft(Ind,k)-360;
            Ind=find(Xright(:,k)>180);
            Xright(Ind,k)=Xright(Ind,k)-360;
            Ind=find(Xhead(:,k)>180);
            Xhead(Ind,k)=Xhead(Ind,k)-360;
        end
        
        % Plot
        h(1)=figure;
        plot(Tright,Xright(:,4:6),Tleft,Xleft(:,4:6))
        hold on
        for k=1:Ntr
            plot([1 1]*Tspawn(k),[-1 1.5],'k:');
            plot([1 1]*Tcol(2*k-1),[-1 1.5],'k');
        end
        title(sprintf('Sub%d, Blk%d - Left and Right XYZ coordinates',sub,blk))
        
        h(2)=figure;
        XR=sqrt(sum(Xright(:,4:6).^2*[1 1 1]',2));
        XL=sqrt(sum(Xleft(:,4:6).^2*[1 1 1]',2));
        plot(Tright,XR,Tleft,XL); hold on
        for k=1:Ntr
            plot([1 1]*Tspawn(k),[.5 1.5],'k:');
            plot([1 1]*Tcol(2*k-1),[.5 1.5],'k');
        end
        ylim([.7 1.5]);
        title(sprintf('Sub%d, Blk%d - Left and Right displacement',sub,blk))
        
        % Amplified hand use
        DataMat=zeros(100,5); % TarLoc, Hand, ReactT, PeakVel, PathLength, Overshoot
        % 1) Target location, frequency
        TarN=zeros(14,1);
        for tr=1:100
            for loc=1:14
                TarName=sprintf('TL%d',loc);
                if strcmp(spawnData{tr},TarName)
                    TarN(loc)=TarN(loc)+1;
                    DataMat(tr,1)=loc;
                end
            end
        end
        % 2) Left/Right hand use per target
        for tr=1:100
            % Hand use
            if tr~=100
                Rmov=max(XR((Tright>Tspawn(tr))&(Tright<Tspawn(tr+1))));
                Lmov=max(XL((Tleft>Tspawn(tr))&(Tleft<Tspawn(tr+1))));
            else
                Rmov=max(XR((Tright>Tspawn(tr))));
                Lmov=max(XL((Tleft>Tspawn(tr))));
            end
            if Rmov>Lmov
                DataMat(tr,2)=1;
            else
                DataMat(tr,2)=2;
            end
        end
        % 3) Reaction time, peak velocity, path length
        for tr=1:100
            Ti=Tspawn(tr); Tf=Tcol(2*tr-1);
            TimeIndR=find((Tright>=Ti)&(Tright<Tf));
            TimeIndL=find((Tleft>=Ti)&(Tleft<Tf));
            if DataMat(tr,2)==1 % Right hand
                Mov=XR(TimeIndR);
                TimeVec=Tright(TimeIndR);
            else
                Mov=XL(TimeIndL);
                TimeVec=Tleft(TimeIndL);
            end
            % a. Reaction time
            MaxM=max(Mov); InitM=mean(Mov(1:10));
            IndI=find(Mov<InitM+(MaxM-InitM)*.05,1,'last');
            RT=TimeVec(IndI)-TimeVec(1);
            DataMat(tr,3)=RT;
            % b. Peak velocity
            dt=1/Fs;
            Vel=(Mov(3:end)-Mov(1:end-2))/(2*dt);
            Vel=[(Mov(2)-Mov(1))/dt;Vel;(Mov(end)-Mov(end-1))/dt];
            % figure,plot(TimeVec,Vel),pause
            DataMat(tr,4)=max(Vel);
            % c. Overshoot
            if tr~=100
                Tnext=Tspawn(tr+1);
            end
            if DataMat(tr,2)==1 % Right hand
                if tr==100
                    Tnext=Tright(end);
                end
                TimeIndR=find((Tright>=Ti)&(Tright<Tnext));
                OS=max(XR(TimeIndR))-Mov(end);
            else
                if tr==100
                    Tnext=Tleft(end);
                end
                TimeIndL=find((Tleft>=Ti)&(Tleft<Tnext));
                OS=max(XL(TimeIndL))-Mov(end);
            end
            DataMat(tr,5)=OS;
            
        end % tr
        
        % Plot: 1. Reaction time
        % - DataMat: TarLoc, Hand, ReactT, PeakVel, PathLength, Overshoot
        h(3)=figure;
        LocPlotInd=[1 8 2 9 3 10 4 11 5 12 6 13 7 14];
        LocNum=[2 2 6 6 10 10 13 13 10 10 6 6 2 2];
        LocCnt=ones(14,1);
        for tr=1:100
            loc=DataMat(tr,1);
            RT=DataMat(tr,3);
            PV=DataMat(tr,4);
            OS=DataMat(tr,5);
            subplot(2,7,LocPlotInd(loc))
            if DataMat(tr,2)==1 % right
                p1=plot(LocCnt(loc),RT,'b*'); hold on
            else % left
                p2=plot(LocCnt(loc),RT,'ro'); hold on
            end
            
            axis([1 LocNum(loc) 0 1]);
            LocCnt(loc)=LocCnt(loc)+1;
        end
        subplot(2,7,1), ylabel('RT (sec)');
        subplot(2,7,4), title(sprintf('Sub%d, Block %d',sub,blk)), legend([p1 p2],'Right','Left');
        subplot(2,7,8), ylabel('RT (sec)');
        subplot(2,7,11), xlabel('Trials');
        
        % Plot: 2. Peak velocity
        % - DataMat: TarLoc, Hand, ReactT, PeakVel, PathLength, Overshoot
        h(4)=figure;
        LocPlotInd=[1 8 2 9 3 10 4 11 5 12 6 13 7 14];
        LocNum=[2 2 6 6 10 10 13 13 10 10 6 6 2 2];
        LocCnt=ones(14,1);
        for tr=1:100
            loc=DataMat(tr,1);
            RT=DataMat(tr,3);
            PV=DataMat(tr,4);
            OS=DataMat(tr,5);
            subplot(2,7,LocPlotInd(loc))
            if DataMat(tr,2)==1 % right
                p1=plot(LocCnt(loc),PV,'b*'); hold on
            else % left
                p2=plot(LocCnt(loc),PV,'ro'); hold on
            end
            
            axis([1 LocNum(loc) 0 2.5]);
            LocCnt(loc)=LocCnt(loc)+1;
        end
        subplot(2,7,1), ylabel('Peak Vel. (m/sec)');
        subplot(2,7,4), title(sprintf('Sub%d, Block %d',sub,blk)), legend([p1 p2],'Right','Left');
        subplot(2,7,8), ylabel('Peak Vel. (m/sec)');
        subplot(2,7,11), xlabel('Trials');
        
        % Plot: 3. Average movement profile
        Nt=Fs*5;
        MovAllR=zeros(Nt,Ntar,14); % Time - Target locations - # of trials per target 
        MovAllL=zeros(Nt,Ntar,14); % Time - Target locations - # of trials per target
        
        NumTr=zeros(Ntar,2); % Target locations, left/right
        LocIndR=zeros(Ntar,1); LocIndL=zeros(Ntar,1);
        Ninit=1;
        if sub==1
            if blk==1
                Ninit=2;
            end
        end
        for tr=Ninit:100
            Ti=Tspawn(tr); 
            if tr<100
                Tf=Tspawn(tr+1);
            else
                if DataMat(tr,2)==1
                    Tf=Tright(end);
                else
                    Tf=Tleft(end);
                end
            end
            
            for loc=1:14
                TarName=sprintf('TL%d',loc);
                if strcmp(spawnData{tr},TarName)
                    LocTr=loc;
                    if DataMat(tr,2)==1
                        LocIndR(loc)=LocIndR(loc)+1;
                    else
                        LocIndL(loc)=LocIndL(loc)+1;
                    end
                end
            end
            %disp(sprintf('tr:%d,L/R:%d, Loc:%d',tr,DataMat(tr,2),LocTr)),pause
            if DataMat(tr,2)==1 % right
                TimeInd=find((Tright>=Ti)&(Tright<Tf)); Nt=length(TimeInd);
                MovAllR(1:Nt,LocTr,LocIndR(LocTr))=XR(TimeInd);
            else
                TimeInd=find((Tleft>=Ti)&(Tleft<Tf)); Nt=length(TimeInd);
                MovAllL(1:Nt,LocTr,LocIndL(LocTr))=XL(TimeInd);
            end
            
        end % tr
        h(5)=figure; FigR=gcf;
        h(6)=figure; FigL=gcf;
        Nt=Fs*5;
        for loc=1:Ntar
            TimeVec=[1:Nt]'/Fs;
            if LocIndR(loc)~=0
                figure(FigR)
                subplot(2,7,LocPlotInd(loc))
                plot(TimeVec,squeeze(MovAllR(1:Nt,loc,1:LocIndR(loc)))); axis([0 3 0.5 1.5]);
            end
            if LocIndL(loc)~=0
                figure(FigL)
                subplot(2,7,LocPlotInd(loc))
                plot(TimeVec,squeeze(MovAllL(1:Nt,loc,1:LocIndL(loc)))); axis([0 3 0.5 1.5]);
            end
        end
        figure(FigR), subplot(2,7,4), title(sprintf('Sub%d, Blk%d - Right hand: movement profile',sub,blk));
        figure(FigL), subplot(2,7,4), title(sprintf('Sub%d, Blk%d - Left hand: movement profile',sub,blk));
        
        savefig(h,sprintf('S%dB%d.fig',sub,blk));
        close all
    end % blk
    
end % sub